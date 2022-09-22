<?php

namespace App\Http\Controllers;

use Spatie\Url\Url;

class LogoutController extends Controller
{
    public function __invoke()
    {
        $cognito = Url::fromString(config('auth.cognito.idp_uri'))
            ->withPath('logout')
            ->withQueryParameter('client_id', config('auth.cognito.clients.web.client_id'))
            ->withQueryParameter('logout_uri', route('logout.success'));

        return redirect()->to((string) $cognito);
    }
}
