<?php

namespace App\Console\Commands;

use App\Models\ProjectMeeting;
use App\Notifications\MeetingReminderNotification;
use Illuminate\Console\Command;

class SendMeetingReminders extends Command
{
    protected $signature = 'meetings:send-reminders';

    protected $description = 'Notifies (email + in-app) all project team members 30 minutes before a scheduled meeting.';

    /** How far ahead of the meeting to notify people, in minutes. */
    private const LEAD_TIME_MINUTES = 30;

    public function handle(): int
    {
        $windowStart = now();
        $windowEnd = now()->addMinutes(self::LEAD_TIME_MINUTES);

        $dueMeetings = ProjectMeeting::query()
            ->whereNull('reminder_sent_at')
            ->whereBetween('meeting_time', [$windowStart, $windowEnd])
            ->with(['project.members.user'])
            ->get();

        if ($dueMeetings->isEmpty()) {
            $this->info('No meeting reminders due.');

            return self::SUCCESS;
        }

        foreach ($dueMeetings as $meeting) {
            $recipients = $meeting->project->members->pluck('user')->filter();

            foreach ($recipients as $user) {
                $user->notify(new MeetingReminderNotification($meeting));
            }

            $meeting->update(['reminder_sent_at' => now()]);

            $this->info("Reminder sent to {$recipients->count()} member(s) for meeting \"{$meeting->title}\".");
        }

        return self::SUCCESS;
    }
}
