<?php

namespace App\Console;

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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
		$schedule->command('inventories:update')->hourly()->appendOutputTo(storage_path('logs/inventories_update.log'));
		//$schedule->command('inventories:update')->dailyAt('18:42')->appendOutputTo(storage_path('logs/inventories_update.log'));
		//$schedule->command('inventories:update')->hourly();
		//$schedule->command('inventories:new')->everyFiveMinutes()->appendOutputTo(storage_path('logs/inventories.log'));
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
