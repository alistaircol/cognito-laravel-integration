<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Url\Url;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $cognito = Url::fromString(config('auth.cognito.idp_uri'))
            ->withPath('login')
            ->withQueryParameter('client_id', config('auth.cognito.clients.web.client_id'))
            ->withQueryParameter('redirect_uri', route('login.success'))
            ->withQueryParameter('response_type', 'code');

        return redirect()->to((string) $cognito);
    }
}
