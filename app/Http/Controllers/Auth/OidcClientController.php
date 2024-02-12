<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Hydra\Client;
use App\Services\OpenIDConnectClient;
use App\Services\OpenIDService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use UnexpectedValueException;
use Vinkla\Hashids\Facades\Hashids;

class OidcClientController extends Controller
{
    private OpenIDService $openIDService;

    public function __construct()
    {
        $this->openIDService = new OpenIDService();
    }

    public function callback(Request $request)
    {
        $data = $request->validate([
            "state"             => "required_with::code|string",
            "error"             => "nullable|required_without:code|string",
            "error_description" => "nullable|required_without:code|string",
            "code"              => "nullable|string",
        ]);
        /**
         * Only Identity Client - Redirects to error page if scope is invalid
         */
        if (isset($data['error'])) {
            return Redirect::route('auth.login');
        }

        /**
         * State Verification
         * Do not delete the default "false" parameter of Session::get
         * otherwise null === null and it would pass the check falsely.
         */
        if ($request->get('state') !== Session::get('login.oauth2state', false)) {
            Session::remove("login.oauth2state");
            return Redirect::route('auth.login');
        }
        Session::flush();
        /**
         * Get Tokens
         */
        $provider = $this->openIDService->setupOIDC($request, $this->clientIsAdmin($request));
        $accessToken = $provider->getAccessToken('authorization_code', [
            'code' => $data['code'],
        ]);
        $userinfoRequest = Http::identity()->withToken($accessToken->getToken())->get("/api/v1/userinfo");
        if ($userinfoRequest->successful() === false) {
            return Redirect::route('auth.login');
        }
        $userinfo = $userinfoRequest->json();

        $identityId = $userinfo['sub'] ?? null;
        if ($identityId === null) {
            throw new UnexpectedValueException("Could not request user id from freshly fetched token.");
        }

        $user = User::where('identity_id', '=', $identityId)->first();
        $user = User::updateOrCreate([
            "identity_id" => $identityId
        ], [
            "identity_id" => $identityId,
            "name" => $userinfo['name'],
            /*
             * Only sync email address on initial sign-in to avoid drift between address used for
             * con registration and DD application. EF
             * TODO: Avoid storing email address entirely by using IDP ID to send email/sync regs
             */
            "email" => $user?->email ?? $userinfo['email'],
            "groups" => $userinfo['groups'],
        ]);
        $user = $user->fresh();
        Auth::loginUsingId($user->id);
        Session::put('access_token', $accessToken);
        Session::put('avatar', $userinfo['avatar']);
        Session::put('name', $userinfo['name']);
        return $this->redirectDestination($request);
    }

    public function login(Request $request): RedirectResponse
    {
        $provider = $this->openIDService->setupOIDC($request, $this->clientIsAdmin($request));
        $authorizationUrl = $provider->getAuthorizationUrl();
        Session::put('login.oauth2state', $provider->getState());
        return Redirect::to($authorizationUrl);
    }

    public function clientIsAdmin(Request $request)
    {
        return false;
    }

    private function redirectDestination(Request $request)
    {
        return Redirect::route('dashboard');
    }
}
