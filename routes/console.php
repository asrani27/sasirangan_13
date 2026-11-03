<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Early Warning System (EWS) dijalankan 1x sehari pada pukul 13:45
Schedule::command('ews')->dailyAt('13:45')->withoutOverlapping();

// Generate data harga bahan pokok harian pada pukul 01:10
Schedule::command('generate:harga')->dailyAt('01:10')->withoutOverlapping();

// Prune log model yang kadaluarsa (misal riwayat cronjob > 30 hari) 1x sehari
Schedule::command('model:prune')->daily();
