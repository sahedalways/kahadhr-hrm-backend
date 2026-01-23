<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// for charging company
Schedule::command('companies:active-company-from-trial')->daily();

Schedule::command('companies:charge')->dailyAt('00:00');

Schedule::command('companies:activate-from-trial')->daily();

Schedule::command('otp:delete-expired')->daily();

Schedule::command('documents:update-expired')->daily();

Schedule::command('chat:clean-temp')->daily();

Schedule::command('leave:reset-carry-over')->daily();

Schedule::command('attendance:auto-clock-out')->dailyAt('00:01');
