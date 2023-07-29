<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        if($request->getUser() != config('services.api.user') && $request->getPassword() != config('services.api.password')) {
            $headers = array('WWW-Authenticate' => 'Basic');
            return response('Unauthorized', 401, $headers);

        }
        return $next($request);
    }
}
