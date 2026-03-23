<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\RefreshToken;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\AccessToken;

class LogoutService implements LogoutServiceInterface
{
    public function __construct(
        private readonly TokenServiceInterface $tokenService,
    ) {}

    /** @param AccessToken<mixed> $accessToken */
    public function logout(AccessToken $accessToken, RefreshToken $refreshToken): void
    {
        DB::beginTransaction();

        try {
            $accessToken->revoke();
            $this->tokenService->deleteRefreshToken($refreshToken);
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        DB::commit();
    }
}
