<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Http\Controllers\Client\RegSysClientController;
use App\Models\TableType;
use App\Notifications\TableAcceptedNotification;
use Illuminate\Http\Request;

class TableVerifyController extends Controller
{
    public function __invoke()
    {
        return view('table.confirm', [
            "application" => \Auth::user()->application
        ]);
    }

    public function view()
    {
        $user = \Auth::user();
        $application = $user->application;

        abort_if($application->type !== ApplicationType::Dealer, 403, 'Shares and Assistants cannot manage this.');

        if ($application->status === ApplicationStatus::TableOffered && $application->is_notified) {
            return view('table.confirm', [
                'application' => $application,
                'table_type_requested' => TableType::find($application->table_type_requested),
                'table_type_assigned' => TableType::find($application->table_type_assigned),
                'table_number' => $application->table_number,
            ]);
        } else {
            return \Redirect::route('dashboard');
        }
    }

    public function update(Request $request)
    {
        $application = \Auth::user()->application;

        abort_if($application->status !== ApplicationStatus::TableOffered, 403, 'No table offer available to be accepted.');

        $assignedTable = $application->assignedTable()->first();

        if (RegSysClientController::bookPackage(\Auth::user()->reg_id, $assignedTable)) {
            $application->setStatusAttribute(ApplicationStatus::TableAccepted);
            \Auth::user()->notify(new TableAcceptedNotification($assignedTable->name, $application->table_number, $assignedTable->price));
            return \Redirect::route('table.confirm')->with('table-confirmation-successful');
        } else {
            return \Redirect::route('table.confirm')->with('table-confirmation-error');
        }
    }


    /*
     * Delete method does not need to be implemented because:
     * - Once accepted, unpaid tables can only be canceled by contacting the team.
     * - Paid tables cannot be canceled at all (at best, transfer via team may be possible).
     */
    // public function delete(Request $request) {}
}
