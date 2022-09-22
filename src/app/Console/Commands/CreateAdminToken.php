<?php

namespace App\Console\Commands;

use App\Models\User;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Credentials\Credentials;
use Aws\Result;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CreateAdminToken extends Command
{
    protected $signature = 'token:create';

    protected $description = 'Create admin token';

    public function handle(): int
    {
        $cognito = new CognitoIdentityProviderClient([
            'region' => 'eu-west-2',
            'version' => '2016-04-18',
            'credentials' => new Credentials(
                config('auth.cognito.aws_access_key'),
                config('auth.cognito.aws_secret_access_key')
            ),
        ]);

        $data = [
            'ClientId'   => config('auth.cognito.clients.system.client_id'),
            'UserPoolId' => config('auth.cognito.user_pool_id'),
            'AuthFlow'   => 'ADMIN_NO_SRP_AUTH',
            'AuthParameters' => [
                'USERNAME' => $user = config('auth.cognito.clients.system.admin.user'),
                'PASSWORD' => config('auth.cognito.clients.system.admin.pass'),
                'SECRET_HASH' => $this->hmacClientSecret($user),
            ],
        ];

        $result = $cognito->adminInitiateAuth($data);

        $response = $this->convertAwsResultToResponse($result);

        /** @var User $user */
        $user = User::query()->where('email', $user)->first();
        $user->setCognitoTokensFromResponse($response);

        return self::SUCCESS;
    }

    private function hmacClientSecret(string $user): string
    {
        return base64_encode(
            hash_hmac(
                'sha256',
                sprintf(
                    '%s%s',
                    $user,
                    config('auth.cognito.clients.system.client_id')
                ),
                config('auth.cognito.clients.system.client_secret'),
                true
            )
        );
    }

    private function convertAwsResultToResponse(Result $result): Response
    {
        $tokens = collect(Arr::get($result, 'AuthenticationResult'))
            ->mapWithKeys(function ($value, $key) {
                return [
                    (string) Str::of($key)->snake() => $value,
                ];
            });

        $metadata = $result->offsetGet('@metadata');
        $status   = Arr::get($metadata, 'statusCode');
        $headers  = Arr::get($metadata, 'headers');

        $response = new \GuzzleHttp\Psr7\Response($status, $headers, $tokens);

        return new Response($response);
    }
}
