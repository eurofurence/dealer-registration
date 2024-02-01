<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TableOfferedShareNotification extends Notification implements ShouldQueue
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
            ->subject(config('con.con_name') . ' Dealers\' Den - Application Accepted')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We are thrilled to inform you that the application for the dealership at ' . config('con.con_name') . ' which you are part of has been accepted! Congratulations!')
            ->line('The table still has to be accepted by Dealership who will also be responsible for paying the table fee, so consult with them in case you have any questions.')
            ->line('Please note that you are required to pay all fees, including the Eurofurence event registration fee, within ' . config('con.payment_timeframe') . ' of receiving this email to secure your placement as a dealer. If payment is overdue, Dealers\' Den management may void your placement.')
            ->line('Thank you again for your interest and participation in ' . config('con.con_name') . ' Dealers\' Den! We are looking forward to seeing you and your beautiful artwork and items on display at the convention, and have no doubt that it will be a huge hit among our attendees.')
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
