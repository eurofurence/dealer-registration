<?php

namespace App\Http\Middleware;

use App\Enums\ApplicationType;
use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

class ForceOverseatingRedirectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(Route::is('applications.invitees.*')) {
            return $next($request);
        }

        /** @var Application */
        $application = Auth::user()->application;
        if(is_null($application) || $application->type !== ApplicationType::Dealer || !$application->isActive()) {
            return $next($request);
        }

        $seats = $application->getSeats();
        if($seats['free'] < 0) {
            return Redirect::route('applications.invitees.view');
        }
        return $next($request);
    }
}
