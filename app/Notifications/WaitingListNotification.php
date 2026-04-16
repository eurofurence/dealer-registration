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

    public function __construct() {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('convention.con_name') . ' Dealers\' Den - Application on Waiting List')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Thank you for submitting your application to sell your amazing art and items at the upcoming ' . config('convention.con_name') . ' and your patience while we have been reviewing the dealer applications.')
            ->line('Unfortunately, all of our available tables have been filled, and we regret to inform you that we cannot accommodate your application at this time. However, we would like to inform you that your application has been put on the waiting list. Should any slots become available before the convention, we will contact you as soon as possible. Also, there might be last-minute sales of canceled dealership spaces on a first-come, first-serve basis at the convention itself, starting 11:00 (noon) on Thursday for unclaimed dealerships. We highly encourage you to drop by in case a spot opens up.')
            ->line('This year, the selection process was incredibly difficult due to the sheer number of applications we received. We made a strong effort to strike a balance between applicants from previous years\' waiting lists, new faces, and returning dealers, while also ensuring a diverse and well-rounded range of offerings.')
            ->line('Please note that not being selected is in no way a reflection of your skills as an artist or dealer, nor does it mean your application was considered insufficient. The overall quality of applications far exceeded the number of tables we have available.')
            ->line('We would also like to highlight the Artist Alley as a great place to showcase and sell your merchandise. Dedicated Staff members will be available on-site during specific “office hours” to assist artists, dealers and attendees. More details will be published on our website soon.')
            ->line('Thank you for your understanding and cooperation. We appreciate your interest in the ' . config('convention.con_name') . ' Dealers\' Den, and we hope that we will be able to work with you in the future.')
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

->line('This year, the selection process was incredibly difficult due to the sheer number of applications we received. We made a strong effort to strike a balance between applicants from previous years\' waiting lists, new faces, and returning dealers, while also ensuring a diverse and well-rounded range of offerings.')

->line('Please note that not being selected is in no way a reflection of your skills as an artist or dealer, nor does it mean your application was considered insufficient. The overall quality of applications far exceeded the number of tables we have available.')

->line('For future events, we intend to place greater emphasis on factors such as how often applicants have participated in the past and how frequently they have been allocated their requested table space.')

->line('We would also like to highlight that, due to the venue move, the Artist Alley will be expanded. Dedicated staff members will be available on-site during specific “office hours” to assist participants. More detailed plans and information will be published on our website soon.')