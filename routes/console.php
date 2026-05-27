<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto backup database 4x sehari (jam 02:00, 08:00, 14:00, 20:00)
Schedule::command('backup:database')->dailyAt('02:00');
Schedule::command('backup:database')->dailyAt('08:00');
Schedule::command('backup:database')->dailyAt('14:00');
Schedule::command('backup:database')->dailyAt('20:00');

