<?php

namespace App\Console;

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
    protected function schedule(Schedule $schedule): void
    {
        // Process expired private stream requests and auto-end streams every 5 minutes
        $schedule->command('private-stream:expire-requests')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->runInBackground();
            
        // You can also run existing content expiration if needed
        // $schedule->command('content:expire')->daily();
        
        // Process expired private stream feedback every hour
        $schedule->command('private-stream:process-expired-feedback')
                 ->hourly()
                 ->description('Process expired private stream feedback periods');
                 
        // Broadcast countdown updates every 10 seconds for active streams for dynamic time display
        $schedule->command('private-stream:broadcast-countdown')
                 ->everyMinute()
                 ->description('Broadcast real-time countdown updates for active private streams');
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
