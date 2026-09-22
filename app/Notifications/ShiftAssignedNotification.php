<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $eventDetails;

    public function __construct($eventDetails)
    {
        $this->eventDetails = $eventDetails;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Shift Assigned')
                    ->greeting('Hello,')
                    ->line('You have been assigned to a new shift.')
                    ->line('Event: ' . $this->eventDetails['event_name'])
                    ->line('Date: ' . $this->eventDetails['date'])
                    ->line('Location: ' . $this->eventDetails['location'])
                    ->action('View My Schedule', route('applicant.schedule'))
                    ->line('Please show up 15 minutes early.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
