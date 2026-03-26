<?php

namespace Tests\Feature\Domain\Recipe;

use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Models\RecipeIngredient;
use App\Domain\Recipe\Models\RecipeStep;
use App\Domain\User\Models\User;
use Database\Seeders\IngredientSeeder;
use Database\Seeders\RecipeSeeder;
use Database\Seeders\UserSeeder;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class RecipeControllerTest extends TestFeatureCase
{
    public function testGetIndexAsOwnerReturnsOk(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->get('/users/' . UserSeeder::USER_ID . '/recipes')
        ;

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'recipes' => [
                'count',
                'items' => [
                    '*' => $this->recipeStructure(),
                ],
            ],
        ]);
        $response->assertJsonPath('recipes.count', 1);
        $response->assertJsonPath('recipes.items.0.id', RecipeSeeder::RECIPE_ID);
        $response->assertJsonPath('recipes.items.0.name', 'Salade de tomates');
    }

    public function testGetIndexAsNotOwnerReturnsForbidden(): void
    {
        $response = $this->getLoggedClient()
            ->get('/users/' . UserSeeder::USER_ID . '/recipes')
        ;

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testGetIndexAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()
            ->get('/users/' . UserSeeder::USER_ID . '/recipes')
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostCreateAsOwnerReturnsCreated(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->post('/users/' . UserSeeder::USER_ID . '/recipes', $this->validCreateBody())
        ;

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'recipe' => [
                'id',
                'name',
                'type',
                'steps',
                'ingredients',
            ],
        ]);

        $recipe = Recipe::where('name', 'Whiskey Sour')->firstOrFail();
        $this->assertSame('cocktail', $recipe->type->value);
        $this->assertCount(2, $recipe->steps);
        $this->assertCount(2, $recipe->recipeIngredients);
    }

    public function testPostCreateAsNotOwnerReturnsForbidden(): void
    {
        $response = $this->getLoggedClient()
            ->post('/users/' . UserSeeder::USER_ID . '/recipes', $this->validCreateBody())
        ;

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPostCreateAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()
            ->post('/users/' . UserSeeder::USER_ID . '/recipes', $this->validCreateBody())
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostCreateWithEmptyBodyReturnsUnprocessableEntity(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->post('/users/' . UserSeeder::USER_ID . '/recipes', [])
        ;

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['message' => 'Validation error']);
    }

    public function testPostCreateWithOtherUserIngredientReturnsUnprocessableEntity(): void
    {
        $otherUser = User::factory()->create();
        $otherIngredient = Ingredient::factory()->create(['user_id' => $otherUser->id]);

        $body = $this->validCreateBody();
        $body['ingredients'] = [
            ['id' => $otherIngredient->id, 'quantity' => 1],
        ];

        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->post('/users/' . UserSeeder::USER_ID . '/recipes', $body)
        ;

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPatchUpdateAsOwnerReturnsOk(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'name' => 'Salade mise à jour',
            ])
        ;

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['recipe' => $this->recipeStructure()]);
        $response->assertJsonPath('recipe.name', 'Salade mise à jour');
        $this->assertDatabaseHas('recipes', ['id' => RecipeSeeder::RECIPE_ID, 'name' => 'Salade mise à jour']);
    }

    public function testPatchUpdateAsNotOwnerReturnsForbidden(): void
    {
        $response = $this->getLoggedClient()
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'name' => 'Hacked',
            ])
        ;

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPatchUpdateAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'name' => 'Hacked',
            ])
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPatchUpdateWithInvalidIdReturnsNotFound(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . self::NONEXISTENT_UUID, [
                'name' => 'Test',
            ])
        ;

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testPatchUpdateWithStepsMergesCorrectly(): void
    {
        $existingStep = RecipeStep::where('recipe_id', RecipeSeeder::RECIPE_ID)->firstOrFail();

        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'steps' => [
                    ['id' => $existingStep->id, 'description' => 'Étape modifiée'],
                    ['description' => 'Nouvelle étape'],
                ],
            ])
        ;

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('recipe_steps', ['id' => $existingStep->id, 'description' => 'Étape modifiée', 'position' => 1]);
        $this->assertDatabaseHas('recipe_steps', ['description' => 'Nouvelle étape', 'position' => 2]);
        $this->assertDatabaseCount('recipe_steps', 2);
    }

    public function testPatchUpdateWithStepsDeletesMissingOnes(): void
    {
        $existingStep = RecipeStep::where('recipe_id', RecipeSeeder::RECIPE_ID)->firstOrFail();

        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'steps' => [
                    ['description' => 'Remplacement total'],
                ],
            ])
        ;

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('recipe_steps', ['id' => $existingStep->id]);
        $this->assertDatabaseHas('recipe_steps', ['description' => 'Remplacement total', 'position' => 1]);
    }

    public function testPatchUpdateWithIngredientsMergesCorrectly(): void
    {
        $existingIngredient = RecipeIngredient::where('recipe_id', RecipeSeeder::RECIPE_ID)->firstOrFail();

        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'ingredients' => [
                    ['id' => $existingIngredient->id, 'quantity' => 5.00],
                    ['ingredient_id' => IngredientSeeder::OIGNON_ID, 'quantity' => 3.00],
                ],
            ])
        ;

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('recipe_ingredients', ['id' => $existingIngredient->id, 'quantity' => 5.00]);
        $this->assertDatabaseHas('recipe_ingredients', ['ingredient_id' => IngredientSeeder::OIGNON_ID, 'quantity' => 3.00]);
        $this->assertDatabaseCount('recipe_ingredients', 2);
    }

    public function testPatchUpdateWithOtherUserIngredientReturnsUnprocessableEntity(): void
    {
        $otherUser = User::factory()->create();
        $otherIngredient = Ingredient::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'ingredients' => [
                    ['ingredient_id' => $otherIngredient->id, 'quantity' => 1],
                ],
            ])
        ;

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPatchUpdateWithStepFromOtherRecipeReturnsUnprocessableEntity(): void
    {
        $otherRecipe = Recipe::factory()->create(['user_id' => UserSeeder::USER_ID]);
        $otherStep = RecipeStep::factory()->create(['recipe_id' => $otherRecipe->id]);

        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'steps' => [
                    ['id' => $otherStep->id, 'description' => 'Hijacked step'],
                ],
            ])
        ;

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPatchUpdateWithRecipeIngredientFromOtherRecipeReturnsUnprocessableEntity(): void
    {
        $otherRecipe = Recipe::factory()->create(['user_id' => UserSeeder::USER_ID]);
        $otherRecipeIngredient = RecipeIngredient::factory()->create([
            'recipe_id' => $otherRecipe->id,
            'ingredient_id' => IngredientSeeder::TOMATE_ID,
        ]);

        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [
                'ingredients' => [
                    ['id' => $otherRecipeIngredient->id, 'quantity' => 99],
                ],
            ])
        ;

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPatchUpdateWithEmptyBodyReturnsOk(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID, [])
        ;

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteDestroyAsOwnerReturnsNoContent(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->delete('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID)
        ;

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('recipes', [
            'id' => RecipeSeeder::RECIPE_ID,
        ]);
    }

    public function testDeleteDestroyAsNotOwnerReturnsForbidden(): void
    {
        $response = $this->getLoggedClient()
            ->delete('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID)
        ;

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteDestroyAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()
            ->delete('/users/' . UserSeeder::USER_ID . '/recipes/' . RecipeSeeder::RECIPE_ID)
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteDestroyWithInvalidIdReturnsNotFound(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->delete('/users/' . UserSeeder::USER_ID . '/recipes/' . self::NONEXISTENT_UUID)
        ;

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @return array<int, string> */
    private function recipeStructure(): array
    {
        return ['id', 'name', 'type', 'steps', 'ingredients'];
    }

    /** @return array<string, mixed> */
    private function validCreateBody(): array
    {
        return [
            'name' => 'Whiskey Sour',
            'type' => 'cocktail',
            'steps' => [
                ['description' => 'Pour whiskey into shaker'],
                ['description' => 'Add lemon juice and shake'],
            ],
            'ingredients' => [
                ['id' => IngredientSeeder::TOMATE_ID, 'quantity' => 1],
                ['id' => IngredientSeeder::OIGNON_ID, 'quantity' => 2],
            ],
        ];
    }
}
