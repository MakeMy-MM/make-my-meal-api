<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\RefreshToken;
use Laravel\Passport\AccessToken;

interface LogoutServiceInterface
{
    /** @param AccessToken<mixed> $accessToken */
    public function logout(AccessToken $accessToken, RefreshToken $refreshToken): void;
}
