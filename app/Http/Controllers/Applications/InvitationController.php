<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Http\Requests\JoinSubmitRequest;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    public function view()
    {
        return view('invitation.enter-code');
    }

    public function store(Request $request)
    {
        $applicationType = Application::determineApplicationTypeByCode($request->get('code'));
        if (!Carbon::parse(config('ef.reg_end_date'))->isFuture() && $applicationType !== ApplicationType::Assistant) {
            throw ValidationException::withMessages([
                "code" => "The registration period for new dealers has ended, please check back next year.",
            ]);
        }
        if ($applicationType === ApplicationType::Assistant && !Carbon::parse(config('ef.assistant_end_date'))->isFuture()) {
            throw ValidationException::withMessages([
                "code" => "The registration period for new assistants has ended.",
            ]);
        }

        $applications = Application::where('type', ApplicationType::Dealer)
            ->where(function ($q) use ($request) {
                return $q->where('invite_code_assistants', $request->get('code'))
                    ->orWhere('invite_code_shares', $request->get('code'));
            })->get();

        if (!$request->get('code')) {
            throw ValidationException::withMessages([
                "code" => "Please enter a code.",
            ]);
        }

        if ($applications->count() === 0) {
            throw ValidationException::withMessages([
                "code" => "Invalid code, please ask the dealership you are trying to join for a new code.",
            ]);
        }

        if ($applications->first()->user_id === \Auth::id()) {
            throw ValidationException::withMessages([
                "code" => "You cannot join your own dealership.",
            ]);
        }

        $application = \Auth::user()->application;
        $action = (!is_null($application) && $application?->type === ApplicationType::Dealer && $application->isActive()) ? "edit" : "create";

        return Redirect::route('applications.' . $action, [
            "code" => $request->get('code'),
        ]);
    }
}
