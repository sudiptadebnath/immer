<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use \App\Http\Controllers\PujaController;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
		
		/*$schedule->call(function () {
			\Log::info("schedular test running");
		})->everyMinute();*/

		/*$schedule->call(function () {
			Log::info("schedular1 running");
			//app(PujaController::class)->sendPujaReminders1(); // FOR REMINDER AT IMMERSION DAY
        })->dailyAt('00:01')->timezone('Asia/Kolkata')->withoutOverlapping();
        
		$schedule->call(function () {
			Log::info("schedular2 running");
			//app(PujaController::class)->sendPujaReminders2(); // FOR REMINDER AT BEFORE 2 HR OF IMMERSION TIME
		})->everyMinute()->withoutOverlapping();*/
		
		/*$schedule->call(function (PujaController $controller) {
			Log::info("schedular2");
			$controller->sendPujaReminders2(); // FOR REMINDER AT BEFORE 2 HR OF IMMERSION TIME
        })->everyMinute()->withoutOverlapping();*/

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
