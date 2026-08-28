<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Task reminders: checks every minute for tasks whose notify_at time has
// arrived and emails + notifies the assigned user.
Schedule::command('tasks:send-reminders')->everyMinute();

// Meeting reminders: checks every minute for meetings starting within the
// next 30 minutes and notifies every project team member.
Schedule::command('meetings:send-reminders')->everyMinute();
