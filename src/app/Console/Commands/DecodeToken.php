<?php

namespace App\Console\Commands;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Console\Command;
use Throwable;

class DecodeToken extends Command
{
    protected $signature = 'token:decode {token : The token to validate}';

    protected $description = 'Decode a token';

    public function handle(): int
    {
        if (!file_exists($file = base_path('jwks.json'))) {
            // run from root with terraform files:
            // curl -o src/jwks.json "$(terraform output -raw cognito_json_web_key_set)"
            $this->alert('No <fg=white>jwks.json</> found in <fg=white>base_path</>');
            $this->info('You can download <fg=white>jwks.json</> at <fg=white>https://cognito-idp.<fg=red>USER_POOL_REGION</>.amazonaws.com/<fg=red>USER_POOL_ID</>/.well-known/jwks.json</>');

            return self::FAILURE;
        }

        $jwks = json_decode(file_get_contents($file), true);
        $keys = JWK::parseKeySet($jwks);

        try {
            $decoded = JWT::decode(
                $this->argument('token'),
                $keys
            );
            dump($decoded);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->alert($e->getMessage());
            return self::FAILURE;
        }
    }
}
