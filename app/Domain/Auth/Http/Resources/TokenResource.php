<?php

namespace App\Domain\Auth\Http\Resources;

use App\Http\Resources\BasicResource;
use Illuminate\Http\Request;

/** @mixin \App\Domain\Auth\DTOs\TokenDTO */
class TokenResource extends BasicResource
{
    public static $wrap = 'tokens';

    public function toArray(Request $request): array
    {
        return [
            'access_token' => [
                'token' => $this->accessToken,
                'type' => $this->accessTokenType,
                'expires_at' => $this->accessTokenExpiresAt->toIso8601String(),
            ],
            'refresh_token' => [
                'token' => $this->refreshToken,
                'expires_at' => $this->refreshTokenExpiresAt->toIso8601String(),
            ],
        ];
    }
}
