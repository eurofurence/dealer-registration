<?php

namespace App\Observers;

use App\Models\Application;
use App\Notifications\PhyiscalChairsChangedNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class ApplicationObserver implements ShouldHandleEventsAfterCommit
{
    // public function created(Application $application)
    // {
    // }

    public function updated(Application $application)
    {
        // If (and only if) the chair assignment has changed ...
        if ($application->wasChanged('physical_chairs')) {
            $physicalChairs = $application->physical_chairs ?? -1;
            // ... and the chair count is valued (i.e. not in "unplanned" state) ...
            if ($physicalChairs >= 0) {
                // ... send a notification email to the main dealer.
                $application->user()->first()->notify(
                    new PhyiscalChairsChangedNotification($application->physical_chairs)
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
