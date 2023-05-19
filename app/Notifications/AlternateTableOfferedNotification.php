<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AlternateTableOfferedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $tableAssigned;
    public int|float $price;

    public function __construct(string $tableAssigned, int|float $price)
    {
        $this->tableAssigned = $tableAssigned;
        $this->price = $price;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting("Dear " . $notifiable->name . ",")
            ->subject(config('ef.con_name') . ' Dealers\' Den - Alternate Table Size')
            ->line('Thank you for your interest in a Dealership at Eurofurence. We appreciate your application and are excited to have you as a potential dealer at the convention.')
            ->line('Unfortunately, we regret to inform you that the table size you have applied for is no longer available. However, we have some alternative table sizes we would like to offer that may be suitable for your needs.')
            ->line('Currently, the following table size is available:')
            ->line($this->tableAssigned . ' - ' . $this->price / 100 . ' EUR')
            ->action('Review Dealership Package', url('/table/confirm'))
            ->line('We understand that this alternative option may not be what you had in mind when applying, but we hope that it will still meet your needs and enable you to participate in the  Eurofurence Dealers\' Den. As we have limited space, we will be offering any available table sizes to other applications on the waiting list if we do not receive a response from you within the next seven days. So please let us know within this time frame whether you would like to accept one of the alternative options or decline the offer and be put on the waiting list. To do so, simply reply to this email.')
            ->line('We apologize for any inconvenience this may have caused you, and we hope to hear back from you soon. Thank you for your understanding and cooperation.')
            ->salutation(new HtmlString('Best regards,<br />the Eurofurence Dealers\' Den Team'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
