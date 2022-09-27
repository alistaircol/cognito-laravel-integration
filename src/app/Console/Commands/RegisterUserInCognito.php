<?php

namespace App\Console\Commands;

use App\Models\User;
use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Credentials\Credentials;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Array_;

class RegisterUserInCognito extends Command
{
    protected $signature = <<<SIGNATURE
    user:create
    {email : The email address of the user }
    {name : The name of the user }
    SIGNATURE;

    protected $description = 'Create a user and register them in Cognito.';

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

        DB::transaction(function () use ($cognito) {
            $result = $cognito->adminCreateUser([
                'UserAttributes' => [
                    [
                        'Name' => 'email',
                        'Value' => $this->argument('email'),
                    ],
                    [
                        'Name' => 'email_verified',
                        'Value' => 'true',
                    ]
                ],
                'UserPoolId' => config('auth.cognito.user_pool_id'),
                'Username' => $this->argument('email'),
            ]);

            /** @var array $user */
            $user = $result->get('User');

            User::query()->create([
                'name' => 'ally',
                'email' => $this->argument('email'),
                'password' => Arr::get($user, 'Username'),
            ]);
        });

        return self::SUCCESS;
    }
}
