<?php

namespace Database\Factories;

use App\Domain\Recipe\Enums\RecipeType;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'type' => fake()->randomElement(RecipeType::cases()),
            'image' => fake()->imageUrl(),
            'user_id' => User::factory(),
        ];
    }
}
