<?php

namespace App\Services\UserGems;

use App\Interfaces\CalendarInterface;
use App\Models\Meeting;
use App\Models\Person;
use App\Models\SalesRepresentative;
use App\Models\UserGems\CalendarApiKey;
use App\Traits\UserGems\CallsUserGemsApi;
use Arr;
use Carbon\Carbon;
use DateTimeInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Str;

class CalendarService implements CalendarInterface
{
    use CallsUserGemsApi;

    private Collection $meetings;

    private int $currentPage = 1;

    private int $meetingsCount;

    private int $resultsPerPage;

    private int $pages;

    public function __construct(
        private readonly PendingRequest $httpClient,
        private readonly Person $people,
        private readonly CalendarApiKey $calendarApiKey,
        private readonly Meeting $meeting,
        private readonly SalesRepresentative $salesRepresentatives,
    ) {
        $this->meetings = collect();
        $this->prepareRequest();
    }

    public function syncMeetingsChangedAfter(string $email, DateTimeInterface $changedAfter = null): void
    {
        $this->loadApiKeyByEmail($email);
        $this->fetchMeetingsThatHaveBeenChangedAfter($changedAfter);
        $this->syncMeetings();
    }

    private function loadApiKeyByEmail(string $email): void
    {
        $key = $this->calendarApiKey->whereRelation('owner', 'email', $email)->value('key');
        $this->httpClient->withToken($key);
    }

    private function fetchMeetingsThatHaveBeenChangedAfter(?DateTimeInterface $dateTime): void
    {
        do {
            $this->fetchNextPage();
            $this->filterMeetingsChangedBefore($dateTime);
        } while ($this->checkWhetherThereMayBeMoreResults());
    }

    private function fetchNextPage(): void
    {
        $response = $this->makeRequest($this->currentPage);
        $this->meetings = $this->meetings->merge($response->json('data'));
        $this->updatePaginationStatistics($response);
    }

    private function makeRequest(): PromiseInterface|Response
    {
        $response = $this->fetchEndpoint();

        if ($response->failed()) {
            $this->handleFailedRequest($response);
        }

        return $response;
    }

    private function fetchEndpoint(): Response|PromiseInterface
    {
        return $this->httpClient
            ->get(
                config('usergems.calendar.endpoint')
                .($this->currentPage == 1 ? '' : '?page='.$this->currentPage)
            );
    }

    private function handleFailedRequest(PromiseInterface|Response $response)
    {
        if ($response->status() == 401) {
            abort(401, 'The UserGems Calendar API key was rejected.');
        } else {
            abort(502, 'The UserGems Calendar API request failed.');
        }
    }

    private function updatePaginationStatistics(PromiseInterface|Response $response): void
    {
        if (! isset($this->meetingsCount)) {
            $this->meetingsCount = $response->json('total');
            $this->resultsPerPage = $response->json('per_page');
            $this->pages = ceil($this->meetingsCount / $this->resultsPerPage);
        }
    }

    private function filterMeetingsChangedBefore(?DateTimeInterface $changedAfter): void
    {
        if (! is_null($changedAfter)) {
            $this->meetings = $this->meetings->filter(function ($meeting) use ($changedAfter) {
                return Carbon::make($meeting['changed'])->isAfter($changedAfter);
            });
        }
    }

    private function checkWhetherThereMayBeMoreResults(): bool
    {
        $shouldContinue = $this->pages > $this->currentPage
            && $this->meetings->count() == $this->resultsPerPage * $this->currentPage;

        if ($shouldContinue) {
            $this->currentPage = $this->currentPage + 1;
        }

        return $shouldContinue;
    }

    public function syncMeetings(): void
    {
        $this->syncMeetingsInformation();
        $this->syncMeetingsAttendeeEmails();
        $this->linkMeetingsToPeople();
    }

    private function syncMeetingsInformation(): void
    {
        $this->meetings
            ->map(function ($meeting) {
                return [
                    'id' => $meeting['id'],
                    'updated_at' => Carbon::parse($meeting['changed']),
                    'starts_at' => Carbon::parse($meeting['start']),
                    'ends_at' => Carbon::parse($meeting['end']),
                    'title' => $meeting['title'],
                ];
            })
            ->chunk(1000)
            ->each(function (Collection $chunk) {
                $this->meeting->upsert($chunk->toArray(), 'id');
            });
    }

    private function syncMeetingsAttendeeEmails()
    {
        $emails = $this->getUniqueListOfEmails();
        $emails = $this->filterCompanyDomainEmails($emails);

        $emails->chunk(1000)
            ->each(function (Collection $chunk) {
                // Unpluck the emails from the collection
                $chunk->transform(function ($email) {
                    return compact('email');
                });
                $this->meeting->people()->upsert($chunk->toArray(), 'email');
            });
    }

    private function getUniqueListOfEmails()
    {
        return $this->meetings
            ->map(function ($meeting) {
                return $meeting['accepted'] + $meeting['rejected'];
            })
            ->flatten()
            ->unique();
    }

    private function filterCompanyDomainEmails(Collection $emails): Collection
    {
        return $emails->filter(function ($email) {
            return ! $this->isCompanyEmail($email);
        });
    }

    private function getAttendeeIdsMappedByEmail(): array
    {
        $people = $this->people->whereIn(
            'email',
            $this->getUniqueListOfEmails())->get(['id', 'email']
            )->toArray();

        $salesReps = $this->salesRepresentatives->whereIn(
            'email',
            $this->getUniqueListOfEmails())->get(['id', 'email']
            )->toArray();

        $attendees = Arr::keyBy(array_merge($people, $salesReps), 'email');

        return Arr::map($attendees, fn ($attendee) => $attendee['id']);
    }

    private function isCompanyEmail($email): bool
    {
        return Str::contains($email, config('company.domain'));
    }

    private function linkMeetingsToPeople()
    {
        $attendeeIds = $this->getAttendeeIdsMappedByEmail();

        $this->meetings->each(function ($meeting) use ($attendeeIds) {
            foreach ($meeting['accepted'] as $email) {
                $this->syncAttendee($email, $meeting['id'], $attendeeIds[$email], true);
            }
            foreach ($meeting['rejected'] as $email) {
                $this->syncAttendee($email, $meeting['id'], $attendeeIds[$email], false);
            }
        });
    }

    private function syncAttendee(string $attendeeEmail, $meetingId, $attendeeId, $accepted): void
    {
        $syncData = [$attendeeId => ['is_accepted' => $accepted]];
        if ($this->isCompanyEmail($attendeeEmail)) {
            $this->meeting->find($meetingId)->salesRepresentatives()->syncWithoutDetaching($syncData);
        } else {
            $this->meeting->find($meetingId)->people()->syncWithoutDetaching($syncData);
        }
    }
}
