<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Spatie\Url\Url;

class LogoutSuccessController extends Controller
{
    public function __invoke()
    {
        if (auth()->guest()) {
            return redirect()->route('index');
        }

        /** @var ?User $user */
        $user = auth()->user();

        if (filled($user->cognito_refresh_token)) {
            $uri  = (string) Url::fromString(config('auth.cognito.idp_uri'))->withPath('/oauth2/revoke');
            $body = [
                'client_id' => config('auth.cognito.clients.web.client_id'),
                'token'     => $user->cognito_refresh_token,
            ];

            Http::asForm()
                ->withBasicAuth(
                    config('auth.cognito.clients.web.client_id'),
                    config('auth.cognito.clients.web.client_secret'),
                )
                ->post($uri, $body);
        }

        $user->resetCognitoTokens();

        Auth::guard('web')->logout();

        return redirect()->route('index');
    }
}
