<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class FrontChannelLogoutController extends Controller
{
    public function __invoke()
    {
        \Auth::logout();
    }
}
