<?php

namespace App\Console;

use App\Jobs\ProcessCrawl;
use App\Jobs\ProcessCrawlMongo;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $nextPage = '"after": ""';
        
        $processCrawlJob = new ProcessCrawl($nextPage);
        $schedule->job($processCrawlJob)->everyFifteenMinutes();

        $processCrawlMongoJob = new ProcessCrawlMongo($nextPage);
        $schedule->job($processCrawlMongoJob)->everyFifteenMinutes();
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
