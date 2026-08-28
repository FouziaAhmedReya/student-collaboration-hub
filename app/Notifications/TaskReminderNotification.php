<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: "'.$this->task->title.'" is due soon')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('This is a reminder about a task assigned to you in "'.$this->task->project->title.'":')
            ->line('**'.$this->task->title.'**')
            ->when($this->task->description, fn ($mail) => $mail->line($this->task->description))
            ->line('Deadline: '.$this->task->deadline->format('l, F j, Y'))
            ->action('View Task', url('/tasks'))
            ->line('Thanks!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'task_reminder',
            'title' => 'Task due soon: '.$this->task->title,
            'body' => 'Project: '.$this->task->project->title.' · Deadline: '.$this->task->deadline->format('M j, Y'),
            'url' => '/tasks',
            'task_id' => $this->task->id,
        ];
    }
}
