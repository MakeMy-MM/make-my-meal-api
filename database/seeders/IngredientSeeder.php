<?php

namespace Database\Seeders;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $this->create('Tomate', MeasurementUnit::KILOGRAM, UserSeeder::USER_ID);
        $this->create('Oignon', MeasurementUnit::GRAM, UserSeeder::USER_ID);
    }

    private function create(string $name, MeasurementUnit $measurementUnit, string $userId): void
    {
        Ingredient::factory()->create([
            'name' => $name,
            'measurement_unit' => $measurementUnit,
            'user_id' => $userId,
        ]);
    }
}
