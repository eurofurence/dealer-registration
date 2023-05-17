<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AlternateTableOfferedShareNotification extends Notification implements ShouldQueue
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
            ->line('Thank you for your interest as share in a Dealership at Eurofurence. We appreciate your application and are excited to have you as a potential dealer at the convention.')
            ->line('Unfortunately, we regret to inform you that the table size your Dealership has applied for is no longer available. However, we have offered your Dealership an alternative table size that may be suitable for your needs.')
            ->line('Currently, the following table size is available:')
            ->line($this->tableAssigned . ' - ' . $this->price / 100 . ' EUR')
            ->line('We understand that this alternative option may not be what you had in mind when applying, but we hope that it will still meet your needs and enable you to participate in the  Eurofurence Dealers\' Den. Please consult with your Dealership about how you would like to proceed, as they are the ones who will have to accept or decline the offer and pay the abovementioned fee.')
            ->salutation(new HtmlString('Best regards,<br />the Eurofurence Dealers\' Den Team'));
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
