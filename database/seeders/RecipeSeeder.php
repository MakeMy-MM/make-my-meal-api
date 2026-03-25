<?php

namespace Database\Seeders;

use App\Domain\Recipe\Enums\RecipeType;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Models\RecipeIngredient;
use App\Domain\Recipe\Models\RecipeStep;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public const string RECIPE_ID = '0196f4a0-6f40-7000-8000-000000000020';

    public function run(): void
    {
        $this->create(
            self::RECIPE_ID,
            'Salade de tomates',
            RecipeType::Starter,
            UserSeeder::USER_ID,
        );
    }

    private function create(string $id, string $name, RecipeType $type, string $userId): void
    {
        Recipe::factory()->create([
            'id' => $id,
            'name' => $name,
            'type' => $type,
            'user_id' => $userId,
        ]);

        RecipeStep::factory()->create([
            'recipe_id' => $id,
            'position' => 1,
            'description' => 'Couper les tomates',
        ]);

        RecipeIngredient::factory()->create([
            'recipe_id' => $id,
            'ingredient_id' => IngredientSeeder::TOMATE_ID,
            'position' => 1,
            'quantity' => 2.00,
        ]);
    }
}
