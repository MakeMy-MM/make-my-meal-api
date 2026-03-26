<?php

namespace App\Domain\Recipe\Inputs;

use App\Domain\Recipe\DTOs\UpdateRecipeDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeIngredientItemDTO;
use App\Domain\Recipe\DTOs\UpdateRecipeStepItemDTO;
use App\Domain\Recipe\Enums\RecipeType;
use App\Domain\Recipe\Http\Requests\UpdateRecipeRequest;
use App\Domain\Recipe\Models\Recipe;
use App\Inputs\InputInterface;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateRecipeInput extends UpdateRecipeDTO implements InputInterface
{
    /**
     * @param UpdateRecipeRequest $data
     * @param array{recipe: Recipe} $models
     */
    public static function fromRequest(FormRequest $data, array $models): static
    {
        $validated = $data->validated();

        $steps = null;
        if (array_key_exists('steps', $validated)) {
            $steps = [];
            foreach ($validated['steps'] as $index => $step) {
                $steps[] = new UpdateRecipeStepItemDTO(
                    id: $step['id'] ?? null,
                    position: $index + 1,
                    description: $step['description'] ?? null,
                );
            }
        }

        $ingredients = null;
        if (array_key_exists('ingredients', $validated)) {
            $ingredients = [];
            foreach ($validated['ingredients'] as $index => $ingredient) {
                $ingredients[] = new UpdateRecipeIngredientItemDTO(
                    id: $ingredient['id'] ?? null,
                    position: $index + 1,
                    ingredientId: $ingredient['ingredient_id'] ?? null,
                    quantity: $ingredient['quantity'] ?? null,
                );
            }
        }

        return new self(
            model: $models['recipe'],
            name: $validated['name'] ?? null,
            type: isset($validated['type']) ? RecipeType::from($validated['type']) : null,
            steps: $steps,
            ingredients: $ingredients,
        );
    }
}
