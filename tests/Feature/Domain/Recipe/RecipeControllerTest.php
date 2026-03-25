<?php

namespace Tests\Feature\Domain\Recipe;

use Database\Seeders\IngredientSeeder;
use Database\Seeders\UserSeeder;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class RecipeControllerTest extends TestFeatureCase
{
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

        $this->assertDatabaseHas('recipes', [
            'name' => 'Whiskey Sour',
            'type' => 'cocktail',
        ]);
        $this->assertDatabaseCount('recipe_steps', 2);
        $this->assertDatabaseCount('recipe_ingredients', 2);
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
