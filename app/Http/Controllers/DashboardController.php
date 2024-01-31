<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Client\RegSysClientController;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        $application = $user->application;
        $efRegistration = RegSysClientController::getSingleReg($user->reg_id);

        return view('dashboard', [
            "application" => $application,
            "efRegistrationStatus" => $efRegistration ? $efRegistration['status'] : false,
        ]);
    }
}
