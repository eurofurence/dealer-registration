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
use App\Notifications\AlternateTableOfferedNotification;
use App\Notifications\AlternateTableOfferedShareNotification;
use App\Notifications\CanceledByDealershipNotification;
use App\Notifications\CanceledBySelfNotification;
use App\Notifications\TableAcceptanceReminderNotification;
use App\Notifications\TableOfferedNotification;
use App\Notifications\TableOfferedShareNotification;
use App\Notifications\WaitingListNotification;
use App\Notifications\WelcomeAssistantNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

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

    /**
     * Export a CSV containing the complete application data.
     */
    public function exportCsvAdmin() {
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

    public function exportAppDataAdmin() {
        abort_if(!\Auth::user()->canAccessFilament(), 403, 'Insufficient permissions');
        return $this->exportAppData();
    }

    /**
     * Export a ZIP containing the CSV for the EF app and the images.
     */
    public function exportAppData() {
        $zipFileName = "appdata.zip";
        $csvName = "applications.csv";

        $handle = fopen(Storage::path($csvName), 'w');
        $applications = Application::getAllApplicationsForAppExport();
        foreach ($applications as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        $zip = new ZipArchive();
        if (true === ($zip->open(Storage::path($zipFileName), ZipArchive::CREATE | ZipArchive::OVERWRITE))) {
            $zip->addFile(Storage::path($csvName), $csvName);
            $zip->close();
        }

        ProfileController::addImagesToZip( $zip, $zipFileName);

        return Storage::download($zipFileName);
    }


    public static function sendStatusNotification(Application $application): StatusNotificationResult
    {
        $user = $application->user()->first();
        $status = $application->getStatus();

        if ($application->type !== ApplicationType::Dealer) {
            Log::info("Not sending accepted notification for user {$user->id} for application {$application->id} since they are not a Dealer.");
            return StatusNotificationResult::NotDealer;
        } else {
            switch ($status) {
                case ApplicationStatus::TableAssigned:
                    // Do not send offer to dealerships where not all shares are accepted
                    if (!$application->isReady()) {
                        Log::info("Not sending accepted notification for user {$user->id} for application {$application->id} since not all Shares or Assistants have been set to TableOffered or Canceled or been assigned the same table number during review.");
                        return StatusNotificationResult::SharesInvalid;
                    }

                    if ($application->table_type_assigned === $application->table_type_requested) {
                        Log::info("Sending accepted notification for table {$application->table_number} (requested: {$application->table_type_requested} | assigned: {$application->table_type_assigned}) to user {$user->id} for application {$application->id}.");
                        $user->notify(new TableOfferedNotification());
                        foreach ($application->children()->get() as $child) {
                            if ($child->type === ApplicationType::Share) {
                                $child->user()->first()->notify(new TableOfferedShareNotification());
                            }
                        }
                        $application->status = ApplicationStatus::TableOffered;
                        return StatusNotificationResult::Accepted;
                    } else {
                        Log::info("Sending on-hold notification for table {$application->table_number} (requested: {$application->table_type_requested} | assigned: {$application->table_type_assigned}) to user {$user->id} for application {$application->id}.");
                        $assignedTable = $application->assignedTable()->first();
                        $user->notify(new AlternateTableOfferedNotification($assignedTable->name, $assignedTable->price));
                        foreach ($application->children()->get() as $child) {
                            if ($child->type === ApplicationType::Share) {
                                $child->user()->first()->notify(new AlternateTableOfferedShareNotification($assignedTable->name, $assignedTable->price));
                            }
                        }
                        $application->status = ApplicationStatus::TableOffered;
                        return StatusNotificationResult::OnHold;
                    }
                case ApplicationStatus::Open:
                    // Do not send dealerships to waiting list if some shares/assistants have a table number
                    if (!$application->isReady()) {
                        Log::info("Not sending application {$application->id} of user {$user->id} to waiting list since some Shares or Assistants have been assigned a table number.");
                        return StatusNotificationResult::SharesInvalid;
                    }

                    Log::info("Sending waiting list notification to user {$user->id} for application {$application->id}.");
                    $user->notify(new WaitingListNotification());
                    foreach ($application->children()->get() as $child) {
                        if ($child->type === ApplicationType::Share) {
                            $child->user()->first()->notify(new WaitingListNotification());
                        }
                    }
                    $application->status = ApplicationStatus::Waiting;
                    return StatusNotificationResult::WaitingList;
                default:
                    Log::info("Not sending notification to user {$user->id} because application {$application->id} is not in an applicable status.");
                    return StatusNotificationResult::StatusNotApplicable;
            }
        }
    }

    public static function sendReminderNotification(Application $application): StatusNotificationResult
    {
        $user = $application->user()->first();
        $status = $application->getStatus();

        if ($application->type !== ApplicationType::Dealer) {
            Log::info("Not sending reminder to user {$user->id} for application {$application->id} since they are not a Dealer.");
            return StatusNotificationResult::NotDealer;
        } else {
            if ($status === ApplicationStatus::TableOffered) {
                $user->notify(new TableAcceptanceReminderNotification());
                return StatusNotificationResult::Accepted;
            } else {
                Log::info("Not sending reminder to user {$user->id} because application {$application->id} is not in an applicable status.");
                return StatusNotificationResult::StatusNotApplicable;
            }
        }
    }

    /**
     * @param Request $request
     * @return ApplicationType
     */
}
