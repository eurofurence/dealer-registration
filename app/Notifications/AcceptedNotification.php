<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $tableAssigned;

    public function __construct(string $tableAssigned)
    {
        $this->tableAssigned = $tableAssigned;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting("Dear " . $notifiable->name . ",")
            ->subject('Approval Phase: Accepted')
            ->line('We are thrilled to inform you that your application for a Dealership at Eurofurence has been accepted! Congratulations!')
            ->line('To confirm your placement as a dealer at Eurofurence, please click on the approval button below. By clicking on this button, you are confirming to abide by the Dealers\' Den\'s terms and conditions (' . config('ef.dealers_tos_url') . ') and the Dealership fee will be added to your Eurofurence Registration.')
            ->line($this->tableAssigned)
            ->action('I confirm my selected Dealership Package.', url('/applications/confirm'))
            ->line('By approving, your Eurofurence event registration will be updated to include the fee for the assigned Dealership. All payments must be handled through the Eurofurence registration system, available at https://identity.eurofurence.org. Please note that you are required to pay all fees, including the Eurofurence event registration fee, within fourteen days of receiving this email to secure your placement as a dealer. If payment is overdue, Dealers\' Den management may void your placement and offer the space to the next dealer on the waiting list.')
            ->line('Upon completion of payment, we will assign a specific table to your Dealership, based on your request and space availability. Although placements are not final until the start of the convention, you will be sent an email containing the preliminary Dealership table assignment for information.')
            ->line('If you have any questions or concerns regarding the payment or subsequent processes, please contact Dealers\' Den management via dealers@eurofurence.org.')
            ->line('Thank you again for your interest and participation in Eurofurence Dealers\' Den! We are looking forward to seeing you and your beautiful artwork and items on display at the convention, and we have no doubt that it will be a huge hit among our attendees.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}