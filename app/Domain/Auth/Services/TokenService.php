<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\TokenDTO;
use App\Domain\Auth\Models\RefreshToken;
use App\Domain\Auth\Repositories\RefreshTokenRepository;
use App\Domain\User\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

class TokenService implements TokenServiceInterface
{
    private readonly string $hashAlgo;
    private readonly int $accessExpiration;
    private readonly int $refreshExpiration;

    public function __construct(
        private readonly RefreshTokenRepository $refreshTokenRepository,
    ) {
        $this->hashAlgo = config('auth.tokens.refresh.hash_algo');
        $this->accessExpiration = config('auth.tokens.access.expiration');
        $this->refreshExpiration = config('auth.tokens.refresh.expiration');
    }

    public function create(User $user): TokenDTO
    {
        $now = CarbonImmutable::now();

        return new TokenDTO(
            accessToken: $this->createAccessToken($user),
            accessTokenType: 'Bearer',
            accessTokenExpiresAt: $now->addMinutes($this->accessExpiration),
            refreshToken: $this->createRefreshToken($user),
            refreshTokenExpiresAt: $now->addMinutes($this->refreshExpiration),
        );
    }

    public function findValidRefreshToken(string $plainToken): ?RefreshToken
    {
        $token = $this->refreshTokenRepository->findByToken(hash($this->hashAlgo, $plainToken));

        if ($token === null || $token->isExpired()) {
            return null;
        }

        return $token;
    }

    public function deleteRefreshToken(RefreshToken $refreshToken): void
    {
        $this->refreshTokenRepository->delete($refreshToken);
    }

    public function deleteAllRefreshTokensForUser(User $user): void
    {
        $this->refreshTokenRepository->deleteAllForUser($user);
    }

    private function createAccessToken(User $user): string
    {
        return $user->createToken('access')->accessToken;
    }

    private function createRefreshToken(User $user): string
    {
        $plainToken = Str::random(64);

        $this->refreshTokenRepository->create(
            $user->id,
            hash($this->hashAlgo, $plainToken),
        );

        return $plainToken;
    }
}
