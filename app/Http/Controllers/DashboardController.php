<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function __invoke()
    {

        $application = $user->application;
        //TODO: Retrieve payment status for Dealer and display status on Dasboard

        return view('dashboard',[
            "application" => $application
        ]);
    }
}
