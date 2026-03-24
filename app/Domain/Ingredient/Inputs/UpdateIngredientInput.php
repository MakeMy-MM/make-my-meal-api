<?php

namespace App\Domain\Ingredient\Inputs;

use App\Domain\Ingredient\DTOs\UpdateIngredientDTO;
use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Http\Requests\UpdateIngredientRequest;
use App\Domain\Ingredient\Models\Ingredient;
use App\Inputs\InputInterface;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateIngredientInput extends UpdateIngredientDTO implements InputInterface
{
    /**
     * @param UpdateIngredientRequest $data
     * @param array{ingredient: Ingredient} $models
     */
    public static function fromRequest(FormRequest $data, array $models): static
    {
        $validated = $data->validated();

        return new self(
            model: $models['ingredient'],
            name: $validated['name'] ?? null,
            measurementUnit: isset($validated['unit']) ? MeasurementUnit::from($validated['unit']) : null,
        );
    }
}
