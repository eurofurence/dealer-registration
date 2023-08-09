<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FrontdeskController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return view('frontdesk', [ 'user' => \Auth::user() ]);
    }

    public function search(Request $request)
    {
        $reg_id = $request->get('reg_id');
        $user = $reg_id ? User::where('reg_id', $reg_id)->first() : false;
        if ($user === false) {
            Log::info("No reg ID provided.");
        } elseif ($user === null) {
            Log::info("User not found.");
        }
        $application = $user ? Application::where('user_id', $user->id)->first() : false;
        if ($application === null) {
            Log::info("No application found for user.");
        }

        return view('frontdesk', [ 'user' => \Auth::user(), 'application' => $application ]);
    }
}
