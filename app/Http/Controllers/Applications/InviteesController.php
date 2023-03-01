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
        $application = $user->applications;

        abort_if($application->type !== ApplicationType::Dealer, 403, 'Shares and Assistants cannot manage this.');
        /**
         * Create invite codes if not existing yet
         */
        if(is_null($application->invite_code_shares)) {
            $application->update(['invite_code_shares' => \Str::random()]);
        }
        if(is_null($application->invite_code_assistants)) {
            $application->update(['invite_code_assistants' => \Str::random()]);
        }


        return view('application.invitees',[
            'application' => $application,
            'currentSeats' => $application->children()->whereNotNull('canceled_at')->count(),
            'maxSeats' => $application->requestedTable->seats,
            "assistants" => $application->children()->where('type',ApplicationType::Assistant)->with('user')->get(),
            "shares" =>  $application->children()->where('type',ApplicationType::Share)->with('user')->get()
        ]);
    }

    public function destroy(InviteeRemovalRequest $request)
    {
        Application::findOrFail($request->get('invitee_id'))->update([
            "type" => ApplicationType::Dealer,
            "canceled_at" => now(),
            "parent" => null
        ]);
        return back();
    }

    public function regenerateKeys()
    {
        $user = \Auth::user()->applications->update([
            'invite_code_assistants' => \Str::random(),
            'invite_code_shares' => \Str::random()
        ]);
        return \Redirect::route('applications.invitees.view');
    }
}
