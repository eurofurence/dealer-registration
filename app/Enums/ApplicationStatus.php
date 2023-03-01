<?php

namespace App\Enums;

enum ApplicationStatus
{
    case Open;
    case Accepted;
    case Allocated;
    case Canceled;
}
