<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DeadlineApproaching extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('A Deadline Approaching')
            ->line('Your deadline is approaching.')
            ->action('View Task', url('/tasks/' . $this->taskId))
            ->line('Thank you for using Deadline!');
    }
}
