<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\DTOs\RegisterDTO;
use App\Domain\User\Models\User;

interface RegisterServiceInterface
{
    public function register(RegisterDTO $dto): User;
}
