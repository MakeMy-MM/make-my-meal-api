<?php

namespace App\Domain\Recipe\Inputs;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeIngredientDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeStepDTO;
use App\Domain\Recipe\Enums\RecipeType;
use App\Domain\Recipe\Http\Requests\CreateRecipeRequest;
use App\Domain\User\Models\User;
use App\Inputs\InputInterface;
use Illuminate\Foundation\Http\FormRequest;

final class CreateRecipeInput extends CreateRecipeDTO implements InputInterface
{
    /**
     * @param CreateRecipeRequest $data
     * @param array{user: User} $models
     */
    public static function fromRequest(FormRequest $data, array $models): static
    {
        $validated = $data->validated();

        $steps = [];
        foreach ($validated['steps'] as $index => $step) {
            $steps[] = new FieldsRecipeStepDTO(
                position: $index + 1,
                description: $step['description'],
            );
        }

        $ingredients = [];
        foreach ($validated['ingredients'] as $index => $ingredient) {
            $ingredients[] = new FieldsRecipeIngredientDTO(
                position: $index + 1,
                ingredientId: $ingredient['id'],
                quantity: $ingredient['quantity'],
            );
        }

        return new self(
            name: $validated['name'],
            type: RecipeType::from($validated['type']),
            userId: $models['user']->id,
            steps: $steps,
            ingredients: $ingredients,
        );
    }
}
