<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Illuminate\Console\Command;

class SendTaskReminders extends Command
{
    protected $signature = 'tasks:send-reminders';

    protected $description = 'Notifies (email + in-app) the assigned user for any task whose notify_at time has passed and no reminder has been sent yet.';

    public function handle(): int
    {
        $dueTasks = Task::query()
            ->whereNotNull('notify_at')
            ->whereNull('reminder_sent_at')
            ->whereNotNull('assigned_user_id')
            ->where('notify_at', '<=', now())
            ->where('status', '!=', 'completed')
            ->with(['assignedUser', 'project'])
            ->get();

        if ($dueTasks->isEmpty()) {
            $this->info('No task reminders due.');

            return self::SUCCESS;
        }

        foreach ($dueTasks as $task) {
            if (! $task->assignedUser) {
                continue;
            }

            $task->assignedUser->notify(new TaskReminderNotification($task));
            $task->update(['reminder_sent_at' => now()]);

            $this->info("Reminder sent to {$task->assignedUser->name} for task \"{$task->title}\".");
        }

        return self::SUCCESS;
    }
}
