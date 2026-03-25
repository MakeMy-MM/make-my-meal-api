<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\User\DTOs\FieldsUserDTO;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoginService implements LoginServiceInterface
{
    public const string INVALID_CREDENTIALS = 'Invalid credentials';

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function login(LoginDTO $dto): User
    {
        $user = $this->userRepository->findFirstByFields(
            new FieldsUserDTO(email: $dto->getEmail()),
        );

        if (!$user || !Hash::check($dto->getPassword(), $user->password)) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, self::INVALID_CREDENTIALS);
        }

        return $user;
    }
}
