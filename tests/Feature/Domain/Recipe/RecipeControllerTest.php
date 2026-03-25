<?php

namespace Tests\Feature\Domain\Recipe;

use App\Domain\Recipe\Models\Recipe;
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
