<?php

namespace Database\Factories;

use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Models\RecipeStep;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecipeStep>
 */
class RecipeStepFactory extends Factory
{
    protected $model = RecipeStep::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'position' => fake()->numberBetween(1, 10),
            'description' => fake()->sentence(),
            'recipe_id' => Recipe::factory(),
        ];
    }
}
