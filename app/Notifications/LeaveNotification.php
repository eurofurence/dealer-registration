<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

/**
 * Notify a dealership that a share or an assistant has left (by themselves or by being kicked out).
 * This is mostly the opposite of @see JoinNotification and thus derived from it.
 */
class LeaveNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $leaveType;
    private string $leaveName;

    public function __construct(string $leaveType, string $leaveName)
    {
        $this->leaveType = ucfirst($leaveType);
        $this->leaveName = $leaveName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('convention.con_name') . ' Dealers\' Den - ' . $this->leaveType . ' Left')
            ->greeting("Dear $notifiable->name,")
            ->line('We wish to inform you that ' . $this->leaveName . ' has left your dealership as ' . $this->leaveType . '.')
            ->line('If you think this was a mistake, you can invite them again: Please go to "Shares & Assistants" in the Dealers\' Den Registration system to generate a new invite code:')
            ->action('Manage Shares and Assistants', url('/applications/invitees'))
            ->salutation(new HtmlString("Best regards,<br />\nthe Eurofurence Dealers' Den Team"));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
