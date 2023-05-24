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

    static function for(\App\Models\Application $application): ApplicationStatus
    {
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

    function orWhere(\Illuminate\Database\Eloquent\Builder $query)
    {
        return match ($this) {
            ApplicationStatus::Canceled => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '!=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '=', null)
                    ->where('waiting_at', '=', null)
            ),
            ApplicationStatus::CheckedIn => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_in_at', '!=', null)
            ),
            ApplicationStatus::Open => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '=', null)
                    ->where('waiting_at', '=', null)
                    ->where(
                        fn (\Illuminate\Database\Eloquent\Builder $query) =>
                        $query->orWhere('table_number', '=', null)
                            ->orWhere(
                                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                                $query->where('table_type_assigned', '=', null)
                                    ->where('type', '=', \App\Enums\ApplicationType::Dealer->value)
                            )
                    )
            ),
            ApplicationStatus::TableAccepted => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('offer_accepted_at', '!=', null)
                    ->where('waiting_at', '=', null)
            ),
            ApplicationStatus::TableAssigned => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '=', null)
                    ->where('waiting_at', '=', null)
                    ->where('table_number', '!=', null)
                    ->where(
                        fn (\Illuminate\Database\Eloquent\Builder $query) =>
                        $query->orWhere('table_type_assigned', '!=', null)
                            ->orWhere('type', '!=', \App\Enums\ApplicationType::Dealer->value)
                    )
            ),
            ApplicationStatus::TableOffered => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '!=', null)
                    ->where('waiting_at', '=', null)
            ),
            ApplicationStatus::Waiting => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '=', null)
                    ->where('waiting_at', '!=', null)
            ),
        };
    }
}
