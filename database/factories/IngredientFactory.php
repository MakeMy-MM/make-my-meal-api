<?php

namespace Database\Factories;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ingredient>
 */
class IngredientFactory extends Factory
{
    protected $model = Ingredient::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'image' => fake()->imageUrl(),
            'measurement_unit' => fake()->randomElement(MeasurementUnit::cases()),
            'user_id' => User::factory(),
        ];
    }
}
