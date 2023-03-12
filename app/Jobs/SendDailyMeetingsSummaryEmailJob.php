<?php

namespace App\Jobs;

use App\Models\SentEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDailyMeetingsSummaryEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly array $salesRepresentativeMeetings
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SentEmail::create([
            'email' => $this->salesRepresentativeMeetings['sales_representative']['email'],
            'body' => $this->salesRepresentativeMeetings,
            'sent_at' => now(),
        ]);
    }
}
