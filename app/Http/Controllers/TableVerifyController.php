<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Http\Controllers\Client\RegSysClientController;
use App\Models\TableType;
use App\Notifications\TableAcceptedNotification;
use App\Notifications\TableAcceptedShareNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TableVerifyController extends Controller
{

    public function view()
    {
        /** @var User */
        $user = Auth::user();
        /** @var Application */
        $application = $user->application;

        abort_if(empty($application), 404, 'Application not found.');
        abort_if($application->type !== ApplicationType::Dealer, 403, 'Shares and Assistants cannot manage this.');

        if ($application->status === ApplicationStatus::TableOffered) {
            return view('table.confirm', [
                'application' => $application,
                'table_type_requested' => TableType::find($application->table_type_requested),
                'table_type_assigned' => TableType::find($application->table_type_assigned),
                'table_number' => $application->table_number,
            ]);
        } else {
            return Redirect::route('dashboard');
        }
    }

    public function update(Request $request)
    {
        /** @var User */
        $user = Auth::user();
        /** @var Application */
        $application = $user->application;
        /** @var null|string */
        $registrationId = $user->reg_id;

        abort_if(empty($application), 404, 'Application not found.');
        abort_if($application->status !== ApplicationStatus::TableOffered, 403, 'No table offer available to be accepted.');
        abort_if($application->type !== ApplicationType::Dealer, 403, 'Shares and Assistants cannot manage this.');

        if (!$registrationId && $registrationId = RegSysClientController::getRegistrationIdForCurrentUser()) {
            $user->update(['reg_id' => $registrationId]);
        }

        $registration = $registrationId ? RegSysClientController::getSingleReg($registrationId) : null;

        if ($registration === null) {
            return Redirect::route('table.confirm')->with('table-confirmation-registration-not-found');
        } elseif ($registration['status'] === 'cancelled' || $registration['status'] === 'new') {
            return Redirect::route('table.confirm')->with('table-confirmation-registration-inactive');
        }

        $assignedTable = $application->assignedTable()->first();

        if (RegSysClientController::bookPackage($registrationId, $assignedTable)) {
            $application->setStatusAttribute(ApplicationStatus::TableAccepted);
            $user->notify(new TableAcceptedNotification($assignedTable->name, $application->table_number, $assignedTable->price));
            foreach ($application->children()->get() as $child) {
                if ($child->type === ApplicationType::Share) {
                    $child->user()->first()->notify(new TableAcceptedShareNotification($assignedTable->name, $application->table_number, $assignedTable->price));
                }
            }
            return Redirect::route('table.confirm')->with('table-confirmation-successful');
        } else {
            return Redirect::route('table.confirm')->with('table-confirmation-error');
        }
    }


    /*
     * Delete method does not need to be implemented because:
     * - Once accepted, unpaid tables can only be canceled by contacting the team.
     * - Paid tables cannot be canceled at all (at best, transfer via team may be possible).
     */
    // public function delete(Request $request) {}
}
