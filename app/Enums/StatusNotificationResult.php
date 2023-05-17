<?php

namespace App\Enums;

enum StatusNotificationResult: string
{
    case Accepted = 'accepted';
    case OnHold = 'on-hold';
    case WaitingList = 'waiting list';
    case SharesInvalid = 'shares invalid';
    case StatusNotApplicable = 'status not applicable';
    case NotDealer = 'not dealer';
}
