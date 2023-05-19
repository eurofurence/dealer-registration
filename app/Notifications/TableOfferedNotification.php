<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TableOfferedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting("Dear " . $notifiable->name . ",")
            ->subject(config('ef.con_name') . ' Dealers\' Den - Application Accepted')
            ->line('We are thrilled to inform you that your application for a dealership at ' . config('ef.con_name') . ' has been accepted! Congratulations!')
            ->line(new HtmlString('To review and confirm your placement as a dealer at ' . config('ef.con_name') . ', please click on the button below. By accepting the offered table, you are agreeing to the <a href="'. config('ef.dealers_tos_url') .'">Dealers\' Den\'s terms and conditions</a>, and the payment process will be initiated.'))
            ->action('Review Dealership Package', url('/table/confirm'))
            ->line(new HtmlString('Once you have confirmed the package, your Eurofurence event registration will be updated to include the fee for the assigned dealership. All payments must be handled through the Eurofurence registration system, available at <a href="'. config('ef.idp_url') .'">' . config('ef.idp_url') . '</a>. Please note that you are required to pay all fees, including the Eurofurence event registration fee, within ' . config('ef.payment_timeframe') . ' of receiving this email to secure your placement as a dealer. If payment is overdue, Dealers\' Den management may void your placement and offer the space to the next dealer on the waiting list.'))
            ->line('Although all placements may be subject to change until the start of the convention, you will be sent an email containing the preliminary dealership table assignment for information.')
            ->line(new HtmlString('If you have any questions or concerns regarding the payment or subsequent processes, please contact Dealers\' Den management via <a href="mailto:'. config('ef.dealers_email') .'">' . config('ef.dealers_email') . '</a>.'))
            ->line('Thank you again for your interest and participation in ' . config('ef.con_name') . ' Dealers\' Den! We are looking forward to seeing you and your beautiful artwork and items on display at the convention, and have no doubt that it will be a huge hit among our attendees.')
            ->salutation(new HtmlString('Best regards,<br />the Eurofurence Dealers\' Den Team'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
