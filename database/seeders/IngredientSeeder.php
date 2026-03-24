<?php

namespace Database\Seeders;

use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $this->create('Tomate', 'kg', $user);
        $this->create('Oignon', 'unit', $user);
    }

    private function create(string $name, string $measurementUnit, User $user): void
    {
        Ingredient::factory()->create([
            'name' => $name,
            'measurement_unit' => $measurementUnit,
            'user_id' => $user->id,
        ]);
    }
}
