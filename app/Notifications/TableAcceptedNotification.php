<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class TableAcceptedNotification extends Notification implements ShouldQueue
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
            ->subject(config('convention.con_name') . ' Dealers\' Den - Package Confirmed and Next Steps')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We have received your confirmation for your dealer package at the ' . config('convention.con_name') . ' Dealers\' Den. Thank you for taking the time to review and confirm your table assignment and dealer package details. We are excited to have you join us as a dealer at the convention!')
            ->line('Your confirmed table assignment details are as follows:')
            ->line('Table Number: ' . $this->tableNumber)
            ->line('Assigned Table Size: ' . $this->tableAssigned)
            ->line('Final Dealer Package Price: ' . $this->price / 100 . ' EUR')
            ->line(new HtmlString('As you have confirmed your dealer package, the final price has been added to your Eurofurence registration fee. To complete the payment, please log in to the Eurofurence registration system at <a href="' . config('convention.idp_url') . '">' . config('convention.idp_url') . '</a> and follow the payment instructions.'))
            ->line('In the coming weeks, we will be sending you more information about the Dealers\' Den setup, event schedules, and other important details to help you prepare for the convention. Please keep an eye on your email for these updates.')
            ->line(new HtmlString('If you have any questions or concerns, feel free to reach out to us at <a href="mailto:' . config('convention.dealers_email') . '">' . config('convention.dealers_email') . '</a>. We are here to help ensure a smooth and enjoyable experience for all our dealers.'))
            ->line('Once again, thank you for your participation in ' . config('convention.con_name') . ' Dealers\' Den. We are looking forward to seeing your amazing art and items showcased at the event!')
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
