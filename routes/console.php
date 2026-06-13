<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Auto-expire stale active exam sessions older than 4 hours
Schedule::call(function () {
    \App\Models\ExamSession::where('status', 'active')
        ->where('started_at', '<', now()->subHours(4))
        ->update(['status' => 'void', 'ended_at' => now()]);
})->hourly()->name('expire-stale-exams')->withoutOverlapping();

// Purge security logs older than 90 days to manage DB size on shared hosting
Schedule::call(function () {
    \App\Models\SecurityLog::where('created_at', '<', now()->subDays(90))->delete();
})->daily()->name('purge-old-security-logs')->withoutOverlapping();

// Shared-hosting-safe cron registration reminder
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

