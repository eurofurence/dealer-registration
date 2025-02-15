<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class WelcomeAssistantNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $dealershipName;

    public function __construct(string $dealershipName)
    {
        $this->dealershipName = $dealershipName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('convention.con_name') . ' Dealers\' Den - Dealer Assistant Information')
            ->greeting('Dear ' . $notifiable->name . ',')
            ->line('We are delighted to welcome you as a Dealer Assistant at ' . config('convention.con_name') . ' Dealers\' Den!')
            ->line('Thank you for accepting the invitation of ' . $this->dealershipName . ' to join their dealership by entering their invitation code. Your support in helping your dealership during setup, teardown, and opening hours is greatly appreciated.')
            ->line('As a Dealer Assistant, you play a vital role in ensuring the smooth operation and success of your dealer\'s experience at the Dealers\' Den. Your assistance will contribute significantly to the overall experience for both your dealer and the attendees.')
            ->line('In the coming weeks, we will be sending you more information about the Dealers\' Den setup, event schedules, and other important details to help you prepare for the convention. Please keep an eye on your email for these updates.')
            ->line('Please note that having a valid registration for Eurofurence by ' . Carbon::parse(config('convention.reg_end_date'))->format('d.m.Y H:i') . ' is required to apply for the Dealers\' Den. Applications for dealerships will only be taken into consideration if all members have a valid registration by that date.')
            ->line(new HtmlString('If you have any questions or concerns, feel free to reach out to us at  <a href="mailto:' . config('convention.dealers_email') . '">' . config('convention.dealers_email') . '</a>.  We are here to help ensure a smooth and enjoyable experience for all our dealers and their assistants.'))
            ->line('We are looking forward to seeing you at Eurofurence and wish you and your dealer a successful and enjoyable experience!')
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
