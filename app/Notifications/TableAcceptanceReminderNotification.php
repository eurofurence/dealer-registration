<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TableAcceptanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('convention.con_name') . ' Dealers\' Den - Reminder')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We recently sent you an email notifying you of your successful application for a dealership at ' . config('convention.con_name') . '. Congratulations again!')
            ->line('However, we noticed that you have yet to confirm your placement as a dealer. This confirmation is crucial to secure your spot in the Dealer\'s Den.')
            ->line('To confirm your dealership, please click on the following button. By doing so, you are agreeing to the Dealers\' Den\'s terms and conditions, and your Eurofurence event registration will be updated to include the fee for the assigned dealership.')
            ->action('Review Dealership Package', url('/table/confirm'))
            ->line(new HtmlString('If you have any questions or concerns regarding the payment or subsequent processes, please contact Dealers\' Den management via <a href="mailto:' . config('convention.dealers_email') . '">' . config('convention.dealers_email') . '</a>.'))
            ->line('Thank you for your prompt attention to this matter. We are excited about your participation in  ' . config('convention.con_name') . ' Dealers\' Den and look forward to seeing your unique pieces on display at the convention.')
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
