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

// Proactive nudge (sarapan 09:00, siang 13:00, malam 20:00)
Schedule::command('diet:nudge')->hourlyAt(0);

// Laporan mingguan setiap Minggu jam 20:00
Schedule::command('diet:weekly-report')->weeklyOn(7, '20:00');

// Auto-recalculate target setiap Senin pagi
Schedule::command('diet:recalculate')->weeklyOn(1, '06:00');

// Auto backup database 4x sehari (jam 02:00, 08:00, 14:00, 20:00)
Schedule::command('backup:database')->dailyAt('02:00');
Schedule::command('backup:database')->dailyAt('08:00');
Schedule::command('backup:database')->dailyAt('14:00');
Schedule::command('backup:database')->dailyAt('20:00');
