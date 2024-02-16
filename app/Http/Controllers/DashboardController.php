<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Client\RegSysClientController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        /** @var User */
        $user = Auth::user();
        $registrationId = $user->reg_id;

        $application = $user->application;

        if (!$registrationId && $registrationId = RegSysClientController::getRegistrationIdForCurrentUser()) {
            $user->update(['reg_id' => $registrationId]);
        }

        $registration = $registrationId ? RegSysClientController::getSingleReg($registrationId) : null;

        return view('dashboard', [
            "application" => $application,
            "registration" => $registration,
        ]);
    }
}
