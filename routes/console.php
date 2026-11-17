<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('rents:generate-installments')
    ->dailyAt('00:10')
    ->withoutOverlapping();

Schedule::command('rents:send-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping();
