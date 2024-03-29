<?php

namespace App\Enums;
use Illuminate\Support\Str;

enum ApplicationStatus: string
{
    case Canceled = 'canceled';
    case Open = 'open';
    case Waiting = 'waiting';
    case TableAssigned = 'table_assigned';
    case TableOffered = 'table_offered';
    case TableAccepted = 'table_accepted';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';

    static function for(\App\Models\Application $application): ApplicationStatus
    {
        if (!is_null($application->canceled_at)) {
            return ApplicationStatus::Canceled;
        } elseif (!is_null($application->checked_out_at)) {
            return ApplicationStatus::CheckedOut;
        } elseif (!is_null($application->checked_in_at)) {
            return ApplicationStatus::CheckedIn;
        } elseif (
            (
                $application->type === \App\Enums\ApplicationType::Dealer
                && !is_null($application->waiting_at)
            )
            || (
                $application->type !== \App\Enums\ApplicationType::Dealer
                && !is_null($application->parent()->first()?->waiting_at)
            )
        ) {
            return ApplicationStatus::Waiting;
        } elseif (
            (
                $application->type === \App\Enums\ApplicationType::Dealer && !is_null($application->offer_accepted_at)
            )
            || (
                $application->type !== \App\Enums\ApplicationType::Dealer
                && !is_null($application->parent()->first()?->offer_accepted_at)
            )
        ) {
            return ApplicationStatus::TableAccepted;
        } elseif (
            (
                $application->type === \App\Enums\ApplicationType::Dealer && !is_null($application->offer_sent_at)
            )
            || (
                $application->type !== \App\Enums\ApplicationType::Dealer
                && !is_null($application->parent()->first()?->offer_sent_at)
            )
        ) {
            return ApplicationStatus::TableOffered;
        } elseif (
            (
                (
                    $application->type === \App\Enums\ApplicationType::Dealer
                    && !empty($application->table_type_assigned)
                )
                || (
                    $application->type !== \App\Enums\ApplicationType::Dealer
                    && !empty($application->parent()->first()?->table_type_assigned)
                )
            )
            && (
                (
                    $application->type === \App\Enums\ApplicationType::Assistant
                    && !empty($application->parent()->first()->table_number)
                )
                || (
                    $application->type !== \App\Enums\ApplicationType::Assistant
                    && !empty($application->table_number)
                )
            )
        ) {
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
            ),
            ApplicationStatus::CheckedOut => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_out_at', '!=', null)
            ),
            ApplicationStatus::CheckedIn => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_out_at', '=', null)
                    ->where('checked_in_at', '!=', null)
            ),
            ApplicationStatus::Waiting => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_out_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('waiting_at', '!=', null)
            ),
            ApplicationStatus::TableAccepted => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_out_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('waiting_at', '=', null)
                    ->where('offer_accepted_at', '!=', null)
            ),
            ApplicationStatus::TableOffered => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_out_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('waiting_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '!=', null)
            ),
            ApplicationStatus::TableAssigned => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_out_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('waiting_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '=', null)
                    ->where('table_number', '!=', null)
                    ->where(
                        fn (\Illuminate\Database\Eloquent\Builder $query) =>
                        $query->orWhere('table_type_assigned', '!=', null)
                            ->orWhere('type', '!=', \App\Enums\ApplicationType::Dealer->value)
                    )
            ),
            ApplicationStatus::Open => $query->orWhere(
                fn (\Illuminate\Database\Eloquent\Builder $query) =>
                $query->where('canceled_at', '=', null)
                    ->where('checked_out_at', '=', null)
                    ->where('checked_in_at', '=', null)
                    ->where('waiting_at', '=', null)
                    ->where('offer_accepted_at', '=', null)
                    ->where('offer_sent_at', '=', null)
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
        };
    }

    function displayName(): string {
        return Str::of($this->value)->replace('_', ' ')->title()->value();
    }
}
