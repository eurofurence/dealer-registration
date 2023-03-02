<?php

namespace App\Enums;

enum ApplicationStatus
{
    case Open;
    case Canceled;
    case Accepted;
    case TableOffered;
    case TableAccepted;
}
