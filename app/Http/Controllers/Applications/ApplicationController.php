<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\StatusNotificationResult;
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
use App\Notifications\WelcomeAssistantNotification;
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
        $application = $request->act();
        if ($application && $application->getStatus() === ApplicationStatus::Open) {
            switch ($application->type) {
                case ApplicationType::Dealer:
                case ApplicationType::Share:
                    \Auth::user()->notify(new WelcomeNotification());
                    break;
                case ApplicationType::Assistant:
                    \Auth::user()->notify(new WelcomeAssistantNotification());
                    break;
                default:
                    abort(400, 'Unknown application type.');
            }
            return \Redirect::route('dashboard')->with('save-successful');
        } else {
            abort(400, 'Invalid application state.');
        }
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
        $application = \Auth::user()->application;
        abort_if($application->status === ApplicationStatus::TableAccepted || $application->status === ApplicationStatus::CheckedIn, 403, 'Applications which have accepted their table may no longer be canceled.');
        return view('application.delete', [
            "application" => $application,
        ]);
    }

    public function destroy()
    {
        $application = \Auth::user()->application;
        abort_if($application->status === ApplicationStatus::TableAccepted || $application->status === ApplicationStatus::CheckedIn, 403, 'Applications which have accepted their table may no longer be canceled.');
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

    public static function sendStatusNotification(Application $application): StatusNotificationResult
    {
        $user = $application->user()->first();
        $status = $application->getStatus();

        if ($application->type !== ApplicationType::Dealer) {
            Log::info("Not sending accepted notification for user {$user->id} for application {$application->id} since they are not a Dealer.");
            return StatusNotificationResult::NotApplicable;
        } elseif (!$application->is_notified) {
            switch ($status) {
                case ApplicationStatus::TableOffered:

                    // Do not notify dealerships where not all Shares have passed review
                    // either by being set to TableOffered or Canceled.
                    $childrenHaveOffer = true;
                    foreach($application->children()->get() as $child) {
                        if ($child->type === ApplicationType::Share) {
                            $childrenHaveOffer = $childrenHaveOffer && ($child->status === ApplicationStatus::TableOffered || $child->status === ApplicationStatus::Canceled);
                        }
                    }
                    if (!$childrenHaveOffer) {
                        Log::info("Not sending accepted notification for user {$user->id} for application {$application->id} since not all Shares have been set to TableOffered or Canceled during review.");
                        return StatusNotificationResult::NotApplicable;
                    }

                    $tableData = $application->assignedTable()->first()->name . ' - ' . $application->assignedTable()->first()->price / 100 . ' EUR';
                    if ($application->table_type_assigned === $application->table_type_requested) {
                        Log::info("Sending accepted notification for table {$application->table_number} (requested: {$application->table_type_requested} | assigned: {$application->table_type_assigned}) to user {$user->id} for application {$application->id}.");
                        $user->notify(new AcceptedNotification($tableData));
                        $application->setIsNotified(true);
                        return StatusNotificationResult::Accepted;
                    } else {
                        Log::info("Sending on-hold notification for table {$application->table_number} (requested: {$application->table_type_requested} | assigned: {$application->table_type_assigned}) to user {$user->id} for application {$application->id}.");
                        $user->notify(new OnHoldNotification($tableData));
                        $application->setIsNotified(true);
                        return StatusNotificationResult::OnHold;
                    }
                case ApplicationStatus::Waiting:
                    Log::info("Sending waiting list notification to user {$user->id} for application {$application->id}.");
                    $user->notify(new WaitingListNotification());
                    $application->setIsNotified(true);
                    return StatusNotificationResult::WaitingList;
                default:
                    Log::info("Not sending notification to user {$user->id} because application {$application->id} is not in an applicable status.");
                    return StatusNotificationResult::NotApplicable;
            }
        } else {
            Log::info("Not sending notification to user {$user->id} for application {$application->id} because notification was already sent previously.");
            return StatusNotificationResult::AlreadySent;
        }
    }

    /**
     * @param Request $request
     * @return ApplicationType
     */
}
