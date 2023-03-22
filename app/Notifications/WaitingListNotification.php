<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class WaitingListNotification extends Notification implements ShouldQueue
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
        ->greeting("Dear ".$notifiable->name.",")
        ->subject('Approval Phase: Waiting List')
        ->line('Thank you for submitting your application to sell your amazing art and items at the upcoming Eurofurence and your patience while we have been reviewing the dealer applications.')
        ->line('Unfortunately, all of our available tables have been filled, and we regret to inform you that we cannot accommodate your application at this time. However, we would like to inform you that your application has been put on the waiting list. Should any slots become available before the convention, we will contact you as soon as possible. Also, there might be last-minute sales of canceled Dealership spaces on a first-come, first-served basis at the convention itself, starting 12:00 (noon) on Monday for unclaimed Dealerships. We highly encourage you to drop by in case a spot opens up.')
        ->line(new HtmlString('If you are also interested in other table sizes, please let us know in time. You can find our offered Dealership Packages at <a href="'. config('ef.dealers_tos_url') .'">' . config('ef.dealers_tos_url') . '</a>.'))
        ->line('Thank you for your understanding and cooperation. We appreciate your interest in the Eurofurence Dealers\' Den, and we hope that we will be able to offer you a Dealership in the future.')
        ->salutation(new HtmlString('Best regards, <br />Pattarchus'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
