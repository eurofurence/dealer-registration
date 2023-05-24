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

    static function for(\App\Models\Application $application): ApplicationStatus {
        if (!is_null($application->canceled_at)) {
            return ApplicationStatus::Canceled;
        } elseif (!is_null($application->checked_in_at)) {
            return ApplicationStatus::CheckedIn;
        } elseif (!is_null($application->waiting_at)) {
            return ApplicationStatus::Waiting;
        } elseif (!is_null($application->offer_accepted_at)) {
            return ApplicationStatus::TableAccepted;
        } elseif (!is_null($application->offer_sent_at)) {
            return ApplicationStatus::TableOffered;
        } elseif (($application->type !== \App\Enums\ApplicationType::Dealer || !is_null($application->table_type_assigned)) && !empty($application->table_number)) {
            return ApplicationStatus::TableAssigned;
        } else {
            return ApplicationStatus::Open;
        }
    }
}
