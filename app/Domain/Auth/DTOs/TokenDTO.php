<?php

namespace App\Domain\Auth\DTOs;

use App\DTOs\DTOInterface;
use Carbon\CarbonImmutable;

class TokenDTO implements DTOInterface
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $accessTokenType,
        public readonly CarbonImmutable $accessTokenExpiresAt,
        public readonly string $refreshToken,
        public readonly CarbonImmutable $refreshTokenExpiresAt,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'access_token_type' => $this->accessTokenType,
            'access_token_expires_at' => $this->accessTokenExpiresAt->toIso8601String(),
            'refresh_token' => $this->refreshToken,
            'refresh_token_expires_at' => $this->refreshTokenExpiresAt->toIso8601String(),
        ];
    }
}
