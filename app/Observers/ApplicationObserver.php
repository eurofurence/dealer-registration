<?php

namespace App\Observers;

use App\Models\Application;
use App\Notifications\PhysicalChairsChangedNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ApplicationObserver implements ShouldHandleEventsAfterCommit
{
    // public function created(Application $application)
    // {
    // }

    public function updated(Application $application)
    {
        // If (and only if) the chair assignment has changed ...
        if (
            $application->wasChanged('physical_chairs') &&
            // ... and this application is a dealer (not share or assistant)
            $application->type?->value == 'dealer' &&
            // ... and is not cancelled
            $application->canceled_at === null
        ) {
            $physicalChairs = $application->physical_chairs ?? -1;
            // ... and the chair count is valued (i.e. not in "unplanned" state) ...
            if ($physicalChairs >= 0) {
                // ... send a notification email to the main dealer.
                $application->user()->first()->notify(
                    new PhysicalChairsChangedNotification($application->physical_chairs)
                );
            }
        }
    }

    // public function deleted(Application $application)
    // {
    // }

    // public function restored(Application $application)
    // {
    // }

    // public function forceDeleted(Application $application)
    // {
    // }
}
