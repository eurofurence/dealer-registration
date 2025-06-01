<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePhysicalChairsRequest;
use App\Http\Requests\InviteeRemovalRequest;
use App\Models\Application;
use App\Notifications\CanceledByDealershipNotification;
use App\Notifications\LeaveNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class InviteesController extends Controller
{
    public function view()
    {
        $user = Auth::user();
        /** @var Application */
        $application = $user->application;

        abort_if($application->type !== ApplicationType::Dealer, 403, 'Shares and Assistants cannot manage this.');
        abort_if(!$application->isActive(), 403, 'Canceled registrations cannot manage this.');
        /**
         * Create invite codes if not existing yet
         */
        if (is_null($application->invite_code_shares)) {
            $application->updateCode('shares');
        }
        if (is_null($application->invite_code_assistants)) {
            $application->updateCode('assistants');
        }

        return view('application.invitees', [
            'application' => $application,
            'seats' => $application->getSeats(),
            'currentSeats' => $application->children()->whereNotNull('canceled_at')->count() + 1,
            'maxSeats' => $application->requestedTable->seats,
            'assistants' => $application->assistants()->get(),
            'shares' =>  $application->shares()->get(),
        ]);
    }

    /**
     * This is the confirmation page for removing a share/assistant from a dealership.
     * It has been added in place of the confirmationless @see InviteesController::destroy().
     */
    public function delete(InviteeRemovalRequest $request)
    {
        $invitee = Application::findOrFail($request->get('invitee_id'));
        // Note: We do not check the end dates here but in the actual destroy handler below.
        // Users should not be routed to this view anyway if they are not allowed to do this.

        return view('application.invitee-delete', [
            "invitee" => $invitee,
        ]);
    }

    /**
     * This is the actual destroy function, which was previously executed without a confirmation page.
     * @param InviteeRemovalRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(InviteeRemovalRequest $request)
    {
        $invitee = Application::findOrFail($request->get('invitee_id'));
        abort_if(!Carbon::parse(config('convention.reg_end_date'))->isFuture() && $invitee->type !== ApplicationType::Assistant, 403, 'Only assistants may be modified once the registration period is over.');
        abort_if(!Carbon::parse(config('convention.assistant_end_date'))->isFuture() && $invitee->type === ApplicationType::Assistant, 403, 'Assistants may no longer be modified once the assistant registration period is over.');

        /** @var ApplicationType $oldApplicationType */
        $oldApplicationType = $invitee->type;
        /** @var ?Application $oldParent */
        $oldParent = $invitee->parent;

        $invitee->update([
            "type" => ApplicationType::Dealer,
            "canceled_at" => now(),
            "parent_id" => null
        ]);
        $invitee->user()->first()->notify(new CanceledByDealershipNotification());

        // Send notification about the leaving share/assistant to the main dealership user.
        // Usually this is the one executing this call, but they should get an email about it nevertheless.
        if ($oldParent) {
            $oldParent->user()->first()->notify(new LeaveNotification($oldApplicationType->value, $invitee->user()->first()->name));
            if ($oldApplicationType === ApplicationType::Share) {
                // Adjust chair count of the previous parent dealership
                $oldParent->applyPhysicalChairsDefaultAdjustment(-1);
            }
        }
        return Redirect::route('applications.invitees.view');
    }

    public function codes(Request $request)
    {
        $clear = true;
        switch ($request->get('action')) {
            case 'clear':
                $clear = true;
                break;
            case 'regenerate':
                $clear = false;
                break;
            default:
                abort(400, 'invalid action');
        }

        abort_if(!Auth::user()->application->updateCode($request->get('type'), $clear), 400, 'invalid code type');

        return Redirect::route('applications.invitees.view');
    }

    /**
     * Route called from User Dashboard to change the number of assigned physical chairs.
     *
     * @param ChangePhysicalChairsRequest $changePhysicalChairsRequest Contains
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeChairs(ChangePhysicalChairsRequest $changePhysicalChairsRequest)
    {
        /** @var Application $application */
        $application = Auth::user()->application;
        abort_if(is_null($application), 404, 'Application not found');
        abort_if(!$application->isActive(), 403, 'Application not active');

        /** @var int $desiredChange */
        $desiredChange = intval($changePhysicalChairsRequest->change_by);

        $result = $application->changePhysicalChairsBy($desiredChange);
        return Redirect::route('applications.invitees.view')->with('physical-chair-change', $result);
    }
}
