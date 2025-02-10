<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteeRemovalRequest;
use App\Models\Application;
use App\Notifications\CanceledByDealershipNotification;
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

    public function destroy(InviteeRemovalRequest $request)
    {
        $invitee = Application::findOrFail($request->get('invitee_id'));
        abort_if(!Carbon::parse(config('convention.reg_end_date'))->isFuture() && $invitee->type !== ApplicationType::Assistant, 403, 'Only assistants may be modified once the registration period is over.');
        abort_if(!Carbon::parse(config('convention.assistant_end_date'))->isFuture() && $invitee->type === ApplicationType::Assistant, 403, 'Assistants may no longer be modified once the assistant registration period is over.');

        $invitee->update([
            "type" => ApplicationType::Dealer,
            "canceled_at" => now(),
            "parent_id" => null
        ]);
        $invitee->user()->first()->notify(new CanceledByDealershipNotification());
        return back();
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
}
