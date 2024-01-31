<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    final public const SESSION_CONFIRMATION_KEY = 'join-confirmation';

    public function view(Request $request)
    {
        $code = $request->input('code');
        $applicationType = Application::determineApplicationTypeByCode($code);

        if (empty($code) || is_null($applicationType)) {
            throw ValidationException::withMessages([
                "code" => "Please enter a valid invitation code.",
            ]);
        }

        if ($applicationType === ApplicationType::Share && !Carbon::parse(config('con.reg_end_date'))->isFuture()) {
            throw ValidationException::withMessages([
                "code" => "The registration period for new dealers and shares has ended, please check back next year.",
            ]);
        }

        if ($applicationType === ApplicationType::Assistant && !Carbon::parse(config('con.assistant_end_date'))->isFuture()) {
            throw ValidationException::withMessages([
                "code" => "The registration period for new assistants has ended.",
            ]);
        }

        $invitingApplication = Application::findByCode($code);

        if (!$invitingApplication) {
            throw ValidationException::withMessages([
                "code" => "Invalid code, please ask the dealership you are trying to join for a new code.",
            ]);
        }

        if ($invitingApplication->user_id === Auth::id()) {
            throw ValidationException::withMessages([
                "code" => "You cannot join your own dealership.",
            ]);
        }

        // Prevent people from sending direct join URLs
        $confirmation = Str::random();
        $request->session()->put(self::SESSION_CONFIRMATION_KEY, $confirmation);

        $application = Auth::user()->application;

        return view('invitation.confirm', [
            'invitingApplication' => $invitingApplication,
            'application' => $application,
            'invitationType' => $applicationType,
            'code' => $request->input('code'),
            'confirmation' => $confirmation,
        ]);
    }

    public function store(Request $request)
    {
        InvitationController::verifyInvitationCodeConfirmation($request);

        $code = $request->get('code');
        $application = Auth::user()->application;
        $action = 'edit';
        if (
            is_null($application)
            || $application->type !== ApplicationType::Dealer
            || !$application->isActive()
        ) {
            $action = 'create';
        }

        return Redirect::route('applications.' . $action, [
            'code' => $code,
            'confirmation' => $request->session()->get(self::SESSION_CONFIRMATION_KEY),
        ]);
    }

    public static function verifyInvitationCodeConfirmation(Request $request)
    {
        // Prevent people from sending direct join URLs
        $confirmation = $request->session()->get(self::SESSION_CONFIRMATION_KEY);
        abort_if(!empty($request->input('code')) && (!$request->session()->has(self::SESSION_CONFIRMATION_KEY) || $confirmation !== $request->input('confirmation')), 400, 'Invalid confirmation code');
    }

    public static function clearInvitationCodeConfirmation(Request $request)
    {
        $request->session()->forget(self::SESSION_CONFIRMATION_KEY);
    }
}
