<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class WelcomeShareNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $dealershipName;

    public function __construct(string $dealershipName)
    {
        $this->dealershipName = $dealershipName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('con.con_name') . ' Dealers\' Den - Application Received')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Thank you for your application as part of your joint dealership with ' . $this->dealershipName . ' at the upcoming Eurofurence. Your interest in being a part of this year\'s Dealers\' Den is very much appreciated.')
            ->line('We have received your application and will review it once the Dealership application period has ended. We understand that waiting can be difficult, but please know that we are working hard to review all applications in a timely manner. Once we have reviewed all the applications, we will get in touch with you to provide you with all the necessary information about the next steps.')
            ->line('Thank you in advance for your patience. The Dealers\' Den management is looking forward to reviewing your application.')
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
