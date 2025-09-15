<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \App\Http\Controllers\PujaController;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
		$schedule->call(function (PujaController $controller) {
			$controller->sendPujaReminders1(); // FOR REMINDER AT IMMERSION DAY
        })->dailyAt('00:01')->timezone('Asia/Kolkata')->withoutOverlapping();
        
		$schedule->call(function (PujaController $controller) {
			$controller->sendPujaReminders2(); // FOR REMINDER AT BEFORE 2 HR OF IMMERSION TIME
        })->everyMinute()->withoutOverlapping();

        // })->everyThirtyMinutes()->withoutOverlapping();
		// })->dailyAt('09:00')->timezone('Asia/Kolkata');
		// })->cron('*/30 * * * *'); 
		// hourly() weeklyOn(1, '08:00') twiceDaily(1, 13)
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
