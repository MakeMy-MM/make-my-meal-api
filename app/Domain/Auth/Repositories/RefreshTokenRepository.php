<?php

namespace App\Domain\Auth\Repositories;

use App\Domain\Auth\Models\RefreshToken;
use App\Domain\User\Models\User;
use App\DTOs\BaseFieldDTO;
use App\Http\Exceptions\InternalServerErrorHttpException;
use App\Repositories\ModelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends ModelRepository<RefreshToken>
 *
 * @method Collection<int, RefreshToken> findByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 * @method RefreshToken|null              findFirstByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 */
class RefreshTokenRepository extends ModelRepository
{
    private readonly int $expiration;

    public function __construct(RefreshToken $model)
    {
        parent::__construct($model);
        $this->expiration = config('auth.tokens.refresh.expiration');
    }

    public function create(string $userId, string $hashedToken): RefreshToken
    {
        try {
            return RefreshToken::create([
                'user_id' => $userId,
                'token' => $hashedToken,
                'expires_at' => now()->addMinutes($this->expiration),
            ]);
        } catch (\Throwable $e) {
            throw new InternalServerErrorHttpException(previous: $e);
        }
    }

    public function findByToken(string $hashedToken): ?RefreshToken
    {
        return RefreshToken::where('token', $hashedToken)->first();
    }

    public function delete(RefreshToken $refreshToken): void
    {
        try {
            $refreshToken->delete();
        } catch (\Throwable $e) {
            throw new InternalServerErrorHttpException(previous: $e);
        }
    }

    public function deleteAllForUser(User $user): void
    {
        try {
            RefreshToken::where('user_id', $user->id)->delete();
        } catch (\Throwable $e) {
            throw new InternalServerErrorHttpException(previous: $e);
        }
    }
}
