<?php

namespace App\Enums;

enum StatusNotificationResult: string
{
    case Accepted = 'accepted';
    case OnHold = 'on-hold';
    case WaitingList = 'waiting list';
    case NotApplicable = 'not applicable';
    case AlreadySent = 'already sent';
}
