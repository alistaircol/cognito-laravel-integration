<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;

/**
 * @method Model mergeFillable(array $fillable)
 * @method Model mergeCasts(array $casts)
 * @method Model setAttribute(string $key, mixed $value)
 * @method mixed getAttribute(string $key)
 * @method Model save(array $options = [])
 */
trait HasCognitoTokens
{
    protected function getCognitoTokenAttributeNames(): array
    {
        return [
            'cognito_access_token',
            'cognito_id_token',
            'cognito_refresh_token',
            'cognito_access_token_expires_at',
        ];
    }

    /**
     * @see \Illuminate\Database\Eloquent\Model::bootTraits()
     * @see \Illuminate\Database\Eloquent\Concerns\GuardsAttributes::mergeFillable()
     * @return void
     */
    protected function initializeHasCognitoTokens(): void
    {
        $this->mergeFillable($this->getCognitoTokenAttributeNames());
        $this->mergeCasts([
            'cognito_access_token_expires_at' => 'datetime',
        ]);
    }

    public function resetCognitoTokens(): void
    {
        foreach ($this->getCognitoTokenAttributeNames() as $attribute) {
            $this->setAttribute($attribute, null);
        }

        $this->save();
    }

    public function setCognitoTokensFromResponse(Response $response): void
    {
        $this->setAttribute('cognito_access_token', $response->json('access_token'));
        $this->setAttribute('cognito_id_token', $response->json('id_token'));
        $this->setAttribute('cognito_refresh_token', $response->json('refresh_token'));
        $this->setAttribute('cognito_access_token_expires_at', now()->addSeconds($response->json('expires_in', 0)));
        $this->save();
    }

    public function getJwtAttribute(): ?string
    {
        return $this->getAttribute('cognito_id_token');
    }
}
