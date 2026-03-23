<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\User\DTOs\FieldsUserDTO;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use App\Http\Exceptions\UnauthorizedHttpException;
use Illuminate\Support\Facades\Hash;

class LoginService implements LoginServiceInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function login(LoginDTO $dto): User
    {
        $user = $this->userRepository->findFirstByFields(
            new FieldsUserDTO(email: $dto->getEmail()),
        );

        if (!$user || !Hash::check($dto->getPassword(), $user->password)) {
            throw new UnauthorizedHttpException();
        }

        return $user;
    }
}
