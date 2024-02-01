<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\StatusNotificationResult;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProfileController;
use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use App\Models\Category;
use App\Models\TableType;
use App\Models\User;
use App\Notifications\AlternateTableOfferedNotification;
use App\Notifications\AlternateTableOfferedShareNotification;
use App\Notifications\CanceledByDealershipNotification;
use App\Notifications\CanceledBySelfNotification;
use App\Notifications\JoinNotification;
use App\Notifications\TableAcceptanceReminderNotification;
use App\Notifications\TableOfferedNotification;
use App\Notifications\TableOfferedShareNotification;
use App\Notifications\WaitingListNotification;
use App\Notifications\WelcomeAssistantNotification;
use App\Notifications\WelcomeNotification;
use App\Notifications\WelcomeShareNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use ZipArchive;

class ApplicationController extends Controller
{
    public function create(Request $request)
    {
        InvitationController::verifyInvitationCodeConfirmation($request);

        $application = Auth::user()->application ?? new Application();
        $categories = Category::orderBy('name', 'asc')->get();
        $code = $request->input('code');
        $applicationType = Application::determineApplicationTypeByCode($code) ?? ApplicationType::Dealer;
        $invitingApplication = self::determineParentByCode($code);

        return view('application.create', [
            'table_types' => TableType::all(['id', 'name', 'price']),
            'application' => $application,
            'categories' => $categories,
            'applicationType' => $applicationType,
            'code' => $code,
            'invitingApplication' => $invitingApplication,
            'confirmation' => $request->session()->get(InvitationController::SESSION_CONFIRMATION_KEY),
            'profile' => ProfileController::getOrCreate($application->id)
        ]);
    }

    public function edit(Request $request)
    {
        InvitationController::verifyInvitationCodeConfirmation($request);

        /** @var Application */
        $application = Auth::user()->application;
        abort_if(is_null($application), 404, 'Application not found');
        if (!$application->isActive()) {
            return Redirect::route('applications.create');
        }
        $code = $request->input('code');
        $applicationType = Application::determineApplicationTypeByCode($code) ?? $application->type;
        $invitingApplication = self::determineParentByCode($code);

        $categories = Category::orderBy('name', 'asc')->get();
        return view('application.edit', [
            'table_types' => TableType::all(['id', 'name', 'price']),
            'application' => $application,
            'categories' => $categories,
            'applicationType' => $applicationType,
            'code' => $code,
            'invitingApplication' => $invitingApplication,
            'confirmation' => $request->session()->get(InvitationController::SESSION_CONFIRMATION_KEY),
            'profile' => ProfileController::getByApplicationId($application->id)
        ]);
    }

    /**
     * Determine application type based on an invite code and check if application for is still possible.
     */
    private static function determineApplicationTypeByCode(string|null $code): ApplicationType|null
    {
        $applicationType = Application::determineApplicationTypeByCode($code);

        abort_if(($applicationType === ApplicationType::Share || $applicationType === ApplicationType::Dealer) && config('con.reg_end_date')->isPast(), 400, "The registration period for new dealers and shares has ended, please check back next year.");
        abort_if($applicationType === ApplicationType::Assistant && config('con.assistant_end_date')->isPast(), 400, "The registration period for new assistants has ended, please check back next year.");

        return $applicationType;
    }

    /**
     * Determine parent application based on an code invite and fail if provided code is invalid.
     */
    private static function determineParentByCode(string|null $code): Application|null
    {
        if (is_null($code)) {
            return null;
        }
        $parent = Application::findByCode($code);
        abort_if(is_null($parent), 404, 'Invalid invite code');

        return $parent;
    }

    public function store(ApplicationRequest $request)
    {
        InvitationController::verifyInvitationCodeConfirmation($request);
        $code = $request->input('code');
        $applicationType = self::determineApplicationTypeByCode($code) ?? ApplicationType::Dealer;

        /** @var Application|null */
        $parent = self::determineParentByCode($code);

        $application = Application::updateOrCreate([
            "user_id" => Auth::id(),
        ], [
            "table_type_requested" => $request->input('space'),
            "type" => $applicationType,
            "display_name" => $request->input('displayName'),
            "website" => $request->input('website'),
            "merchandise" => $request->input('merchandise'),
            "is_afterdark" => $request->input('denType') === "denTypeAfterDark",
            "is_power" => $request->input('power') === "on",
            "is_wallseat" => $request->input('wallseat') === "on",
            "wanted_neighbors" => $request->input('wanted'),
            "comment" => $request->input('comment'),
            "parent_id" => $parent?->id,
            "invite_code_shares" => null,
            "invite_code_assistants" => null,
            "waiting_at" => null,
            "offer_sent_at" => null,
            "canceled_at" => null,
            "table_number" => null,
        ]);

        if ($applicationType !== ApplicationType::Assistant) {
            // TODO: Refactor
            ProfileController::createOrUpdate($request, $application->id);
        }

        InvitationController::clearInvitationCodeConfirmation($request);

        if (!$application || $application->getStatus() !== ApplicationStatus::Open) {
            abort(400, 'Invalid application state');
        }

        /** @var User */
        $user = Auth::user();

        switch ($application->type) {
            case ApplicationType::Dealer:
                $user->notify(new WelcomeNotification());
                break;
            case ApplicationType::Share:
                $user->notify(new WelcomeShareNotification($parent->getFullName()));
                $parent->user->notify(new JoinNotification($application->type->value, $application->getFullName()));
                break;
            case ApplicationType::Assistant:
                $user->notify(new WelcomeAssistantNotification($parent->getFullName()));
                $parent->user->notify(new JoinNotification($application->type->value, $application->getFullName()));
                break;
            default:
                abort(400, 'Unknown application type.');
        }
        return Redirect::route('dashboard')->with('save-successful');
    }

    public function update(ApplicationRequest $request)
    {
        InvitationController::verifyInvitationCodeConfirmation($request);
        /** @var User */
        $user = Auth::user();
        /** @var Application */
        $application = $user->application;
        abort_if(is_null($application), 404, 'Application not found');

        $code = $request->input('code');
        $newApplicationType = self::determineApplicationTypeByCode($code);

        $newParent = self::determineParentByCode($code);

        $application->update([
            "table_type_requested" => $request->input('space'),
            "type" => $newApplicationType ?? $application->type,
            "display_name" => $request->input('displayName'),
            "website" => $request->input('website'),
            "merchandise" => $request->input('merchandise'),
            "is_afterdark" => $request->input('denType') === "denTypeAfterDark",
            "is_power" => $request->input('power') === "on",
            "is_wallseat" => $request->input('wallseat') === "on",
            "wanted_neighbors" => $request->input('wanted'),
            "comment" => $request->input('comment'),
            "parent_id" => $newParent?->id ?? $application->parent_id,
        ]);

        if ($application->isActive() && $newApplicationType !== ApplicationType::Assistant) {
            // TODO: Refactor
            ProfileController::createOrUpdate($request, $application->id);
        }

        InvitationController::clearInvitationCodeConfirmation($request);

        if ($newParent) {
            $newParent->user->notify(new JoinNotification($newApplicationType->value, $user->name));
            if ($newApplicationType === ApplicationType::Assistant) {
                $user->notify(new WelcomeAssistantNotification($newParent->getFullName()));
            } elseif ($newApplicationType === ApplicationType::Share) {
                $user->notify(new WelcomeShareNotification($newParent->getFullName()));
            }
        }

        return Redirect::route('applications.edit')->with('save-successful');
    }

    public function delete()
    {
        $application = Auth::user()->application;
        abort_if(is_null($application), 404, 'Application not found');
        abort_if($application->status === ApplicationStatus::TableAccepted || $application->status === ApplicationStatus::CheckedIn, 403, 'Applications which have accepted their table may no longer be canceled.');

        return view('application.delete', [
            "application" => $application,
        ]);
    }

    public function destroy()
    {
        /** @var User $user */
        $user = Auth::user();
        $application = $user->application;
        abort_if(is_null($application), 404, 'Application not found');
        abort_if($application->status === ApplicationStatus::TableAccepted || $application->status === ApplicationStatus::CheckedIn, 403, 'Applications which have accepted their table may no longer be canceled.');

        foreach ($application->children()->get() as $child) {
            $child->update([
                'canceled_at' => now(),
                'parent_id' => null,
                'type' => 'dealer'
            ]);
            $child->user()->first()->notify(new CanceledByDealershipNotification());
        }

        $application->update([
            'canceled_at' => now(),
            'parent_id' => null,
            'type' => 'dealer'
        ]);
        $user->notify(new CanceledBySelfNotification());

        return Redirect::route('dashboard');
    }

    /**
     * Export a CSV containing the complete application data.
     */
    public function exportCsvAdmin()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->isAdmin(), 403, 'Insufficient permissions');

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

    public function exportAppDataAdmin()
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->isAdmin(), 403, 'Insufficient permissions');
        return $this->exportAppData();
    }

    /**
     * Export a ZIP containing the CSV for the EF app and the images.
     */
    public function exportAppData()
    {
        $zipFileName = "appdata.zip";
        $csvFileName = "applications.csv";
        $separator = ";";

        $csvFile = tmpfile();
        $csvFileUri = stream_get_meta_data($csvFile)['uri'];
        $applications = Application::getAllApplicationsForAppExport();

        if (!empty($applications)) {
            # add table headers
            array_unshift($applications, array_keys($applications[0]));
        }

        foreach ($applications as $row) {
            fputcsv($csvFile, $row, $separator);
        }
        fflush($csvFile);

        $zipFile = tmpfile();
        $zipFileUri = stream_get_meta_data($zipFile)['uri'];
        $zip = new ZipArchive();
        abort_if(!$zip->open($zipFileUri, ZipArchive::CREATE), 500, 'Failed to create ZIP archive for export');

        $zip->addFromString($csvFileName, file_get_contents($csvFileUri));
        ProfileController::addImagesToZip($zip, $zipFileName);
        fflush($zipFile);

        return response()->streamDownload(function () use ($zipFile, $zipFileUri) {
            echo file_get_contents($zipFileUri);
        }, $zipFileName);
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
}
