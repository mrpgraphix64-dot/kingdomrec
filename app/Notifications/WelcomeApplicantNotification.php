<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeApplicantNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $applicantName;

    public function __construct($applicantName)
    {
        $this->applicantName = $applicantName;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Welcome to Kingdom Recruitments!')
                    ->greeting('Hello ' . $this->applicantName . ',')
                    ->line('Thank you for registering with Kingdom Recruitments.')
                    ->line('Please make sure to complete your profile and upload your CV if you haven\'t already.')
                    ->action('Go to Dashboard', route('applicant.profile'))
                    ->line('We look forward to finding the perfect shifts for you!');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
