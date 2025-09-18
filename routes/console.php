<?php

use App\Http\Controllers\PujaController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily reminder (immersion day)
Schedule::call(function () {
    //Log::info("schedular1 and 2");
    app(PujaController::class)->sendPujaReminders1();
    app(PujaController::class)->sendPujaReminders2();
})->everyMinute();
