<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MailNotificationToStorageListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSending $event): void
    {
        if (method_exists($event->notification, 'toMail')) {
            $mail = $event->notification->toMail($event->notifiable)->render();
            $path = 'mails/' . date('c') . '-' . str_replace('@', '__at__', $event->notifiable->email) . '-' . substr(sha1(random_bytes(32)), 0, 8) . '.html';
            Storage::put($path, $mail);
            Log::info("Wrote notification mail to {$path}.");
        }
    }
}
