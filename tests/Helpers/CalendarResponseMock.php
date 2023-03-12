<?php

namespace Tests\Helpers;

use Database\Factories\MeetingFactory;

class CalendarResponseMock
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    public function __construct(private readonly MeetingFactory $meetingFactory)
    {
    }

    public function generateResponse(
        array $meetings,
        int $meetingsPerPage = null,
    ): array {
        $meetingsPerPage = $meetingsPerPage ?? rand(5, 15);
        $meetingsCount = count($meetings);
        $pages = ceil($meetingsCount / $meetingsPerPage);

        $responsePages = [];
        for ($i = 1; $i <= $pages; $i++) {
            $responsePages[$i] = (object) FixtureData::fromFixtureFile(
                __DIR__.'/../Fixtures/UserGems/CalendarApiService/base_response.json',
                [
                    'TOTAL_RESULTS' => $meetingsCount,
                    'RESULTS_PER_PAGE' => $meetingsPerPage,
                    'CURRENT_PAGE' => $i,
                ]
            );
        }

        return $responsePages;
    }
}
