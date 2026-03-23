<?php

namespace App\Domain\Auth\Inputs;

use App\Domain\Auth\DTOs\RegisterDTO;
use App\Domain\Auth\Http\Requests\RegisterRequest;
use App\Inputs\InputInterface;
use Illuminate\Foundation\Http\FormRequest;

final class RegisterInput extends RegisterDTO implements InputInterface
{
    /** @param RegisterRequest $data */
    public static function fromRequest(FormRequest $data, array $models): static
    {
        $validated = $data->validated();

        return new self(
            email: $validated['email'],
            password: $validated['password'],
        );
    }
}
