<?php

namespace App\Services;

use App\Interfaces\CalendarInterface;
use App\Interfaces\PersonEnrichmentInterface;
use App\Models\Meeting;
use App\Models\Person;
use App\Models\SalesRepresentative;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class DailyMeetingsSummaryService
{
    public function __construct(
        private readonly SalesRepresentative       $salesRepresentative,
        private readonly CalendarInterface         $calendar,
        private readonly PersonEnrichmentInterface $personEnrichment,
    )
    {
    }

    public function generateMeetingDataForAllSalesRepresentativesForDate(DateTimeInterface $date): BaseCollection
    {
        $allMeetingsData = collect();

        foreach ($this->salesRepresentative->all() as $salesRepresentative) {
            $currentSalesRepMeetings = $this->getSalesRepresentativeUpdatedMeetings($salesRepresentative, $date);
            if ($currentSalesRepMeetings->isNotEmpty()) {
                $this->enrichPeopleDataForAllMeetings($currentSalesRepMeetings);
                $this->buildResponse($allMeetingsData, $salesRepresentative, $currentSalesRepMeetings);
            }
        }

        return $allMeetingsData;
    }

    private function enrichPeopleDataForAllMeetings(Collection $currentSalesRepMeetings): void
    {
        $currentSalesRepMeetings->each(function (Meeting $meeting) {
            $meeting->people->each(function (Person $person) {
                $this->enrichPersonDataIfExpired($person);
            });
        });
    }

    private function enrichPersonDataIfExpired(Person $person): void
    {
        $lastEnrichedAt = $person->last_enriched_at;
        if (is_null($lastEnrichedAt) || $lastEnrichedAt->isBefore(now()->subMonth())) {
            $this->personEnrichment->enrichPersonDataByEmail($person->email);
            $person->last_enriched_at = now();
            $person->save();
            $person->refresh();
        }
    }

    private function getSalesRepresentativeUpdatedMeetings(
        SalesRepresentative $salesRepresentative,
        DateTimeInterface   $date
    ): Collection
    {
        $this->calendar->syncMeetingsChangedAfter(
            $salesRepresentative->email,
            $salesRepresentative->meetings_synced_at
        );

        return $salesRepresentative->getMeetingsForDate(Carbon::make($date)->startOfDay());
    }

    private function buildResponse(
        BaseCollection      $allMeetingsData,
        SalesRepresentative $salesRepresentative,
        Collection          $currentSalesRepMeetings,
    ): void
    {
        $allMeetingsData->push([
            'sales_representative' => $salesRepresentative->only(['name', 'email']),
            'meetings' => $this->buildMeetingsAttribute($currentSalesRepMeetings, $salesRepresentative),
        ]);
    }

    private function buildSalesRepresentativesAttribute(
        Meeting             $meeting,
        SalesRepresentative $salesRepresentative
    ): BaseCollection
    {
        return $meeting
            ->salesRepresentatives
            ->except($salesRepresentative->id)
            ->map(function (SalesRepresentative $salesRepresentative) {
                return [
                    'name' => $salesRepresentative->name,
                    'email' => $salesRepresentative->email,
                    'accepted' => $salesRepresentative->pivot->is_accepted,
                ];
            });
    }

    private function buildPeopleAttribute(
        Meeting             $meeting,
        SalesRepresentative $salesRepresentative
    ): BaseCollection
    {
        return $meeting->people->map(function (Person $person) use ($salesRepresentative) {
            return [
                'name' => $person->first_name . ' ' . $person->last_name,
                'accepted' => $person->pivot->is_accepted,
                'linkedin_profile_url' => $person->linkedin_profile_url,
                'avatar_url' => $person->avatar_url,
                'title' => $person->title,
                'email' => $person->email,
                'meetings_count' => $person->meetings()
                    ->pluck('id')->intersect($salesRepresentative->meetings()->pluck('id'))->count(),
                'meetings_with_other_sales_representatives' =>
                    $this->getCountOfMeetingsWithOtherSalesRepresentatives($salesRepresentative, $person),
            ];
        });
    }

    private function buildMeetingsAttribute(
        Collection          $currentSalesRepMeetings,
        SalesRepresentative $salesRepresentative
    ): BaseCollection
    {
        return $currentSalesRepMeetings->map(function (Meeting $meeting) use ($salesRepresentative) {
            return [
                'start_time' => $meeting->starts_at->toTimeString(),
                'end_time' => $meeting->ends_at->toTimeString(),
                'title' => $meeting->title,
                'duration' => $meeting->ends_at->diffInMinutes($meeting->starts_at),
                'sales_representatives' => $this->buildSalesRepresentativesAttribute($meeting, $salesRepresentative),
                'company' => $meeting->people->first()->fresh()->clientCompany,
                'people' => $this->buildPeopleAttribute($meeting, $salesRepresentative),
            ];
        });
    }

    private function getCountOfMeetingsWithOtherSalesRepresentatives(
        SalesRepresentative $salesRepresentative,
        Person              $person
    ): BaseCollection
    {
        return \DB::table('attendee_meeting', 'am')
            ->leftJoin('sales_representatives as sr', 'sr.id', '=', 'am.attendee_id')
            ->where('attendee_type', SalesRepresentative::class)
            ->where('attendee_id', '!=', $salesRepresentative->id)
            ->whereIn('meeting_id', $person->meetings()->pluck('id'))
            ->selectRaw('sr.name, count(attendee_id) as count')
            ->groupBy('attendee_id')
            ->get();
    }
}
