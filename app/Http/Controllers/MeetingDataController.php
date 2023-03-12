<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeetingDataRequest;
use App\Services\DailyMeetingsSummaryService;
use Date;

class MeetingDataController extends Controller
{
    public function __invoke(MeetingDataRequest $request, DailyMeetingsSummaryService $dailyMeetingsSummaryService)
    {
        return response()->json([
            $dailyMeetingsSummaryService
                ->generateMeetingDataForAllSalesRepresentativesForDate(Date::make($request->date)),
        ]);
    }
}
