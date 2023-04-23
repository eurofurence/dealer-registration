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

        if ($application->status === ApplicationStatus::TableOffered) {
            return view('table.confirm', [
                'application' => $application,
                'table_type_requested' => TableType::find($application->table_type_requested),
                'table_type_assigned' => TableType::find($application->table_type_assigned),
                'table_number' => $application->table_number,
            ]);
        } else if ($application->status === ApplicationStatus::TableAccepted) {
            return view('table.confirm', [
                'application' => $application,
                'table_type_requested' => TableType::find($application->table_type_requested),
                'table_type_assigned' => TableType::find($application->table_type_assigned),
                'table_number' => $application->table_number,
            ]);
        } else {
            return view('dashboard');
        }
    }

    public function update(Request $request)
    {
        $application = \Auth::user()->application;

        if (RegSysClientController::bookPackage(\Auth::user()->reg_id, TableType::find($application->table_type_assigned))) {
            $application->setStatusAttribute(ApplicationStatus::TableAccepted);
            \Auth::user()->notify(new TableAcceptedNotification());
            return \Redirect::route('table.confirm')->with('table-confirmation-successful');
        } else {
            return \Redirect::route('table.confirm')->with('table-confirmation-error');
        }
    }


    public function delete(Request $request)
    {
        $application = \Auth::user()->application;

        if (RegSysClientController::removePackage(\Auth::user()->reg_id, TableType::find($application->table_type_assigned))) {
            // TODO
        } else {
            // TODO
        }
    }
}
