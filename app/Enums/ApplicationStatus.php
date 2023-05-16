<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case Canceled = 'canceled';
    case Open = 'open';
    case Waiting = 'waiting';
    case TableAssigned = 'table_assigned';
    case TableOffered = 'table_offered';
    case TableAccepted = 'table_accepted';
    case CheckedIn = 'checked_in';
}
