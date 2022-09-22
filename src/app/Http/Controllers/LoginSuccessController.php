<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Spatie\Url\Url;

class LoginSuccessController extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. Get an access token using the authentication code
        // 2. Get the user info using the access token to do some auth on our side
        $code = $request->input('code');

        // https://docs.aws.amazon.com/cognito/latest/developerguide/token-endpoint.html
        $uri  = (string) Url::fromString(config('auth.cognito.idp_uri'))->withPath('oauth2/token');
        $body = [
            'client_id'    => config('auth.cognito.clients.web.client_id'),
            'grant_type'   => 'authorization_code',
            'redirect_uri' => route('login.success'),
            'code'         => $code,
        ];

        $responseTokens = Http::asForm()
            ->withBasicAuth(
                config('auth.cognito.clients.web.client_id'),
                config('auth.cognito.clients.web.client_secret'),
            )
            ->post($uri, $body);

        // https://docs.aws.amazon.com/cognito/latest/developerguide/userinfo-endpoint.html
        $uri     = (string) Url::fromString(config('auth.cognito.idp_uri'))->withPath('oauth2/userInfo');
        $token   = $responseTokens->json('access_token');
        $headers = [
            'Authorization' => 'Bearer ' . $token,
        ];

        $responseUserInfo = Http::withHeaders($headers)->get($uri);

        /** @var User $user */
        $user = User::query()
            ->where('email', $responseUserInfo->json('email'))
            ->firstOrFail();

        $user->setCognitoTokensFromResponse($responseTokens);

        Auth::guard('web')->login($user);

        return redirect()->route('index');
    }
}
