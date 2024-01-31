<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class JoinNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private string $joinType;
    private string $joinName;

    public function __construct(string $joinType, string $joinName)
    {
        $this->joinType = ucfirst($joinType);
        $this->joinName = $joinName;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(config('con.con_name') . ' Dealers\' Den - ' . $this->joinType . ' Joined')
            ->greeting("Dear $notifiable->name,")
            ->line('We wish to inform you that ' . $this->joinName . ' has successfully joined your dealership as ' . $this->joinType . ' via your invite code.')
            ->line('If you did not invite them, please go to "Shares & Assistants" in the Dealers\' Den Registration system to generate a new invite code or disable invitations and remove them from your dealership:')
            ->action('Manage Shares and Assistants', url('/applications/invitees'))
            ->salutation(new HtmlString('Best regards,<br />the Eurofurence Dealers\' Den Team'));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
