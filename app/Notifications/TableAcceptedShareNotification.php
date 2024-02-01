<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TableAcceptedShareNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $tableAssigned;
    private string $tableNumber;
    private string $price;

    public function __construct(string $tableAssigned, string $tableNumber, string $price)
    {
        $this->tableAssigned = $tableAssigned;
        $this->tableNumber = $tableNumber;
        $this->price = $price;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('con.con_name') . ' Dealers\' Den - Package Confirmed and Next Steps')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('Your Dealership has successfully confirmed its package at the ' . config('con.con_name') . ' Dealers\' Den. We are excited to have you join us as a dealer at the convention!')
            ->line('The table assignment details are as follows:')
            ->line('Table Number: ' . $this->tableNumber)
            ->line('Assigned Table Size: ' . $this->tableAssigned)
            ->line('Final Dealer Package Price (paid by Dealership): ' . $this->price / 100 . ' EUR')
            ->line('As you are part of a Dealership, please be aware that the abovementioned price will be charged to the Dealership and not to you.')
            ->line('In the coming weeks, we will be sending you more information about the Dealers\' Den setup, event schedules, and other important details to help you prepare for the convention. Please keep an eye on your email for these updates.')
            ->line(new HtmlString('If you have any questions or concerns, feel free to reach out to us at <a href="mailto:' . config('con.dealers_email') . '">' . config('con.dealers_email') . '</a>. We are here to help ensure a smooth and enjoyable experience for all our dealers.'))
            ->line('Once again, thank you for your participation in ' . config('con.con_name') . ' Dealers\' Den. We are looking forward to seeing your amazing art and items showcased at the event!')
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
