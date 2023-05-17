<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CanceledBySelfNotification extends Notification implements ShouldQueue
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
            ->subject(config('ef.con_name') . ' Dealers\' Den - Application Canceled')
            ->greeting("Dear ".$notifiable->name.",")
            ->line('This mail confirms that you have successfully canceled your registration for the Dealers\' Den at this year\'s Eurofurence. We are sorry to see you go and would love to see you back next year.')
            ->line('Should you change your mind, you can resubmit your application or join another dealership until the end of the application phase.')
            ->salutation(new HtmlString('Best regards,<br />the Eurofurence Dealers\' Den Team'));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
