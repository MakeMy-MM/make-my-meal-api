<?php

namespace Database\Seeders;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public const string TOMATE_ID = '0196f4a0-6f40-7000-8000-000000000010';
    public const string OIGNON_ID = '0196f4a0-6f40-7000-8000-000000000011';

    public function run(): void
    {
        $this->create(self::TOMATE_ID, 'Tomate', MeasurementUnit::KILOGRAM, UserSeeder::USER_ID);
        $this->create(self::OIGNON_ID, 'Oignon', MeasurementUnit::GRAM, UserSeeder::USER_ID);
    }

    private function create(?string $id, string $name, MeasurementUnit $measurementUnit, string $userId): void
    {
        Ingredient::factory()->create(array_filter([
            'id' => $id,
            'name' => $name,
            'measurement_unit' => $measurementUnit,
            'user_id' => $userId,
        ]));
    }
}
