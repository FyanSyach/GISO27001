<?php

// Menyiapkan periode challenge secara idempoten; scheduler wajib dijalankan operator.
Schedule::call(function (): void {
    Artisan::call('db:seed', ['--class' => ChallengeSeeder::class, '--force' => true]);
})->daily()->name('prepare-learning-challenges')->withoutOverlapping();

use Database\Seeders\ChallengeSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
