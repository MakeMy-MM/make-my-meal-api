<?php

namespace App\Domain\Auth\Inputs;

use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\Http\Requests\LoginRequest;
use App\Inputs\InputInterface;
use Illuminate\Foundation\Http\FormRequest;

final class LoginInput extends LoginDTO implements InputInterface
{
    /** @param LoginRequest $data */
    public static function fromRequest(FormRequest $data, array $models): static
    {
        $validated = $data->validated();

        return new self(
            email: $validated['email'],
            password: $validated['password'],
        );
    }
}
