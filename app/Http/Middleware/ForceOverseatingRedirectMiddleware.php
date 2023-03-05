<?php

namespace App\Http\Middleware;

use App\Enums\ApplicationType;
use Closure;
use Illuminate\Http\Request;

class ForceOverseatingRedirectMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(\Route::is('applications.invitees.*')) {
            return $next($request);
        }

        $application = \Auth::user()->application;
        if(is_null($application) || $application->type !== ApplicationType::Dealer || !is_null($application->canceled_at)) {
            return $next($request);
        }

        $isTooManyAssistant = $application?->getFreeShares() < 0;
        $isTooManyShare = $application?->getFreeAssistants() < 0;
        if($isTooManyAssistant || $isTooManyShare) {
            return \Redirect::route('applications.invitees.view');
        }
        return $next($request);
    }
}
