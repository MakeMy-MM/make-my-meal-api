<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\TokenDTO;
use App\Domain\Auth\Models\RefreshToken;
use App\Domain\User\Models\User;

interface TokenServiceInterface
{
    public function create(User $user): TokenDTO;

    public function findValidRefreshToken(string $plainToken): ?RefreshToken;

    public function deleteRefreshToken(RefreshToken $refreshToken): void;

    public function deleteAllRefreshTokensForUser(User $user): void;
}
