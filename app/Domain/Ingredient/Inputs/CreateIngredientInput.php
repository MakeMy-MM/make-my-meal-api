<?php

namespace App\Domain\Ingredient\Inputs;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Http\Requests\CreateIngredientRequest;
use App\Domain\User\Models\User;
use App\Inputs\InputInterface;
use Illuminate\Foundation\Http\FormRequest;

final class CreateIngredientInput extends CreateIngredientDTO implements InputInterface
{
    /**
     * @param CreateIngredientRequest $data
     * @param array{user: User} $models
     */
    public static function fromRequest(FormRequest $data, array $models): static
    {
        $validated = $data->validated();

        return new self(
            name: $validated['name'],
            measurementUnit: MeasurementUnit::from($validated['unit']),
            userId: $models['user']->id,
        );
    }
}
