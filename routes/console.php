<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cek pengingat setiap menit
Schedule::command('reminders:send')->everyMinute();

// Kirim ringkasan harian jam 21:00
Schedule::command('reminders:daily-summary')->dailyAt('21:00');

// Auto backup database jam 02:00
Schedule::command('backup:database')->dailyAt('02:00');
