<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\User\Models\User;

interface LoginServiceInterface
{
    public function login(LoginDTO $dto): User;
}
