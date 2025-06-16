<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PhysicalChairsChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private int $newChairCount;

    public function __construct(int $newChairCount)
    {
        $this->newChairCount = $newChairCount;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        // Note: When changing the text here, please check that the assertion
        // in \Tests\Feature\ChairNotificationTest::assertChairChangeNotification still matches!
        return (new MailMessage)
            ->subject(config('convention.con_name') . ' Dealers\' Den - Physical Chairs')
            ->greeting("Dear $notifiable->name,")
            ->line(new HtmlString(sprintf('Currently, your dealership will receive <b>%d physical chairs</b>.', $this->newChairCount)))
            ->line('This year, we kindly ask you to tell us how many physical chairs (stools to sit on) you actually need for your dealership.')
            ->line(new HtmlString(sprintf(
                'You can change how many chairs you need <b>until %s</b> in our dashboard. <i>After this deadline, requesting more chairs is not possible!</i>',
                Carbon::parse(config('convention.physical_chairs_end_date'))->format('d.m.Y H:i')
            )))
            ->action('Manage dealership chair count', url('/applications/invitees'))
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
