<?php

namespace App\Jobs;

use App\Services\DailyMeetingsSummaryService;
use Date;
use DateTimeInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class GenerateDailyMeetingsDataAndSendEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Carbon|DateTimeInterface $date;

    public function __construct(string $date = null)
    {
        $this->date = $date ? Date::make($date) : now();
    }

    /**
     * Execute the job.
     */
    public function handle(DailyMeetingsSummaryService $dailyMeetingsSummaryService): void
    {
        $allSalesRepresentativeMeetings = $dailyMeetingsSummaryService
            ->generateMeetingDataForAllSalesRepresentativesForDate($this->date);

        foreach ($allSalesRepresentativeMeetings as $salesRepresentativeMeetings) {
            dispatch(new SendDailyMeetingsSummaryEmailJob($salesRepresentativeMeetings));
        }
    }
}
