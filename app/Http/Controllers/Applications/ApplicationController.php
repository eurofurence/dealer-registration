<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\TableType;
use App\Notifications\AcceptedNotification;
use App\Notifications\CanceledByDealershipNotification;
use App\Notifications\CanceledBySelfNotification;
use App\Notifications\OnHoldNotification;
use App\Notifications\WaitingListNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        $application = \Auth::user()->application ?? new Application();
        $applicationType = Application::determineApplicationTypeByCode($request->get('code'));
        return view('application.create', [
            'table_types' => TableType::all(['id', 'name', 'price']),
            'application' => $application,
            'applicationType' => $applicationType,
            'code' => $request->get('code'),
            'profile' => ProfileController::getOrCreate($application->id)
        ]);
    }

    public function store(ApplicationRequest $request)
    {
        $request->act();
        \Auth::user()->notify(new WelcomeNotification());
        return \Redirect::route('dashboard')->with('save-successful');
    }

    public function edit(Request $request)
    {
        $application = \Auth::user()->application;
        abort_if(is_null($application), 403, 'No Registration');
        $applicationType = ($request->get('code')) ? Application::determineApplicationTypeByCode($request->get('code')) : $application->type;
        return view('application.edit', [
            'table_types' => TableType::all(['id', 'name', 'price']),
            "application" => $application,
            'applicationType' => $applicationType,
            'code' => $request->get('code'),
            'profile' => ProfileController::getByApplicationId($application->id)
        ]);
    }
    public function update(ApplicationRequest $request)
    {
        $request->act();
        return \Redirect::route('applications.edit')->with('save-successful');
    }

    public function delete()
    {
        return view('application.delete', [
            "application" => \Auth::user()->application,
        ]);
    }

    public function destroy()
    {
        $application = \Auth::user()->application;
        foreach ($application->children()->get() as $child) {
            $child->update([
                'canceled_at' => now(),
                'parent' => null,
                'type' => 'dealer'
            ]);
            $child->user()->first()->notify(new CanceledByDealershipNotification());
        }
        $application->update([
            'canceled_at' => now(),
            'parent' => null,
            'type' => 'dealer'
        ]);
        \Auth::user()->notify(new CanceledBySelfNotification());
        return \Redirect::route('dashboard');
    }

    public function exportCsv()
    {
        abort_if(!\Auth::user()->canAccessFilament(), 403, 'Insufficient permissions');

        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=applications.csv',
            'Expires' => '0',
            'Pragma' => 'public'
        ];

        $applications = Application::getAllApplicationsForExport();

        if (!empty($applications)) {
            # add table headers
            array_unshift($applications, array_keys($applications[0]));
        }

        $callback = function () use ($applications) {
            $handle = fopen('php://output', 'w');
            foreach ($applications as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers)->sendContent();
    }

    public static function sendStatusNotification(Application $application) {
        $user = $application->user()->first();
        $status = $application->getStatus();

        if (!$application->is_notified){
            switch ($status){
                case ApplicationStatus::TableOffered:
                    $tableData = $application->assignedTable()->first()->name . ' - ' . $application->assignedTable()->first()->price/100 . ' EUR';
                    if ($application->assignedTable()->first()->name === $application->requestedTable()->first()->name) {
                        $user->notify(new AcceptedNotification($tableData));
                    } else {
                        $user->notify(new OnHoldNotification($tableData));
                    }
                    $application->setIsNotified(true);
                    break;
                case ApplicationStatus::Waiting:
                    $user->notify(new WaitingListNotification());
                    $application->setIsNotified(true);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @param Request $request
     * @return ApplicationType
     */
}
