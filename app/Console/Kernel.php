<?php

namespace App\Console;

use App\Jobs\RefreshQuickBooksTokenJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\TestCommand::class,
        Commands\DateWiseStockCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
		// $schedule->command('test:run')->everyMinute();
		$schedule->command('app:date-wise-stock-command')->dailyAt("12:00");
        $schedule->job(new RefreshQuickBooksTokenJob())->everyThirtyMinutes();
        $schedule->job(new \App\Jobs\SyncQuickBooksCustomersJob)->everyMinute();
        // $schedule->command('inspire')
        //          ->hourly();
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
