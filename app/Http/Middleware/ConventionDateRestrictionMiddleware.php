<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

/**
 * Restrict access for non-staff outside of registration and convention phases.
 */
class ConventionDateRestrictionMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        if (
            // No redirect during reg/event time …
            Carbon::now(config('convention.timezone'))->isBetween(config('convention.reg_start_date'), config('convention.con_end_date'))
            // … or if user is DD staff.
            || ($user->isAdmin() || $user->isFrontdesk())
        ) {
            if (Route::is('closed')) {
                return Redirect::route('dashboard');
            } else {
                return $next($request);
            }
        }

        // Prevent circular redirects
        if (Route::is('closed')) {
            return $next($request);
        }

        return Redirect::route('closed');
    }
}
