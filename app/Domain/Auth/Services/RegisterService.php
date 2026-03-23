<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\RegisterDTO;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;

class RegisterService implements RegisterServiceInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function register(RegisterDTO $dto): User
    {
        DB::beginTransaction();

        try {
            $user = $this->userRepository->create($dto);
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        DB::commit();

        return $user;
    }
}
