<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteeRemovalRequest;
use App\Models\Application;

class InviteesController extends Controller
{
    public function view()
    {
        $user = \Auth::user();
        $application = $user->application;

        abort_if($application->type !== ApplicationType::Dealer, 403, 'Shares and Assistants cannot manage this.');
        abort_if(!$application->isActive(), 403, 'Canceled registrations cannot manage this.');
        /**
         * Create invite codes if not existing yet
         */
        if(is_null($application->invite_code_shares)) {
            $application->update(['invite_code_shares' => "dealers-".\Str::random()]);
        }
        if(is_null($application->invite_code_assistants)) {
            $application->update(['invite_code_assistants' => "assistant-".\Str::random()]);
        }

        return view('application.invitees',[
            'application' => $application,
            'currentSeats' => $application->children()->whereNotNull('canceled_at')->count() + 1,
            'maxSeats' => $application->requestedTable->seats,
            "assistants" => $application->children()->where('type',ApplicationType::Assistant)->with('user')->get(),
            "shares" =>  $application->children()->where('type',ApplicationType::Share)->with('user')->get(),
            "shares_count" => $application->getAvailableShares(),
            "assistants_count" => $application->getAvailableAssistants(),
            "shares_active_count" => $application->getActiveShares(),
            "assistants_active_count" => $application->getActiveAssistants(),
        ]);
    }

    public function destroy(InviteeRemovalRequest $request)
    {
        $invitee = Application::findOrFail($request->get('invitee_id'));

        $invitee->update([
            "type" => ApplicationType::Dealer,
            "canceled_at" => now(),
            "parent" => null
        ]);
        //TODO: Notify effected assistant/share about cancellation of parent Dealership
        //$child->user()->notify(new CanceledByDealershipNotification());
        return back();
    }

    public function regenerateKeys()
    {
        $user = \Auth::user()->application->update([
            'invite_code_assistants' => "assistant-".\Str::random(),
            'invite_code_shares' => "dealers-".\Str::random()
        ]);
        return \Redirect::route('applications.invitees.view');
    }
}
