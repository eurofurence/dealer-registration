<?php

namespace App\Http\Controllers\Applications;

use App\Enums\ApplicationType;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    public function view()
    {
        return view('invitation.enter-code');
    }

    public function store(Request $request)
    {
        $applications = Application::where('type', ApplicationType::Dealer)
            ->where(function ($q) use ($request) {
                return $q->where('invite_code_assistants', $request->get('code'))
                    ->orWhere('invite_code_shares', $request->get('code'));
            })->get();

        if($applications->count() === 0) {
            throw ValidationException::withMessages([
                "code" => "Invalid code, please ask your dealer for a new code."
            ]);
        }

        abort_if($applications->first()->user_id === \Auth::id(),403,"Cannot add to own application.");

        if($applications->first()->invite_code_assistants === $request->get('code')) {
            return view('application.create')->with([
                "invite_code" => $request->get('code')
            ]);
        }

        if($applications->first()->invite_code_shares === $request->get('code')) {
            return view('application.create')->with([
                "invite_code" => $request->get('code')
            ]);
        }
    }
}
