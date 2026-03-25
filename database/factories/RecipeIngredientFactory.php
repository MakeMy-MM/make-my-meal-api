<?php

namespace Database\Factories;

use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Models\RecipeIngredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecipeIngredient>
 */
class RecipeIngredientFactory extends Factory
{
    protected $model = RecipeIngredient::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'position' => fake()->numberBetween(1, 10),
            'quantity' => fake()->randomFloat(2, 0.01, 100),
            'ingredient_id' => Ingredient::factory(),
            'recipe_id' => Recipe::factory(),
        ];
    }
}
