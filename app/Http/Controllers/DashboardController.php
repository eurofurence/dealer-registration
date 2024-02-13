<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Client\RegSysClientController;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $registrationId = $user->reg_id;

        $application = $user->application;

        if (!$registrationId && $registrationId = RegSysClientController::getRegistrationIdForCurrentUser()) {
            $application->user()->update(['reg_id' => $registrationId]);
        }

        $registration = $registrationId ? RegSysClientController::getSingleReg($registrationId) : null;

        return view('dashboard', [
            "application" => $application,
            "registration" => $registration,
        ]);
    }
}
