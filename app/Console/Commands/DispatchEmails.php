<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDailyMeetingsDataAndSendEmailsJob;
use Illuminate\Console\Command;

class DispatchEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches the daily meetings e-mails';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        dispatch(new GenerateDailyMeetingsDataAndSendEmailsJob());
    }
}
