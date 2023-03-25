<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CanceledByDealershipNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Registration Status: Canceled by Dealership')
            ->greeting("Dear ".$notifiable->name.",")
            ->line('The dealership you had joined has chosen to remove you from their application, thereby canceling your own application. But don\'t worry, all data you had entered remains in our system, allowing you pick one of the following options without having to reenter everything:')
            ->line('- Ask the dealership you were part of to allow you to rejoin if you think this happened in error,')
            ->line('- Get in touch with other dealers and join a different dealership or')
            ->line('- Apply for a full dealership yourself.')
            ->line('Thank you for your understanding.')
            ->salutation(new HtmlString('Best regards,<br />the Eurofurence Dealers\' Den Team'));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
