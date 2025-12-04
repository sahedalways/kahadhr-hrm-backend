<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// for charging company
Schedule::command('companies:charge')->dailyAt('00:00');

Schedule::command('otp:delete-expired')->dailyAt('00:00');

Schedule::command('documents:update-expired')->dailyAt('00:00');

Schedule::command('chat:clean-temp')->dailyAt('00:00');

Schedule::command('leave:reset-carry-over')->dailyAt('00:00');
