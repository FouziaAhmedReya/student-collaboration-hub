<?php

namespace App\Notifications;

use App\Models\ProjectMeeting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public ProjectMeeting $meeting)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Upcoming meeting: '.$this->meeting->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line('You have an upcoming meeting for "'.$this->meeting->project->title.'":')
            ->line('**'.$this->meeting->title.'**')
            ->when($this->meeting->agenda, fn ($mail) => $mail->line($this->meeting->agenda))
            ->line('When: '.$this->meeting->meeting_time->format('l, F j, Y g:i A'))
            ->action('View Meeting', url('/meetings'))
            ->line('See you there!');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'meeting_reminder',
            'title' => 'Upcoming meeting: '.$this->meeting->title,
            'body' => 'Project: '.$this->meeting->project->title.' · '.$this->meeting->meeting_time->format('M j, g:i A'),
            'url' => '/meetings',
            'meeting_id' => $this->meeting->id,
        ];
    }
}
