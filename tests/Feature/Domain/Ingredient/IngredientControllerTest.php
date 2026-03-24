<?php

namespace Tests\Feature\Domain\Ingredient;

use Database\Seeders\UserSeeder;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class IngredientControllerTest extends TestFeatureCase
{
    public function testGetIndexAsOwnerReturnsOk(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->get('/users/' . UserSeeder::USER_ID . '/ingredients')
        ;

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'ingredients' => [
                'count',
                'items' => [
                    '*' => $this->ingredientStructure(),
                ],
            ],
        ]);
        $response->assertJsonPath('ingredients.count', 2);
    }

    public function testGetIndexAsNotOwnerReturnsForbidden(): void
    {
        $response = $this->getLoggedClient()
            ->get('/users/' . UserSeeder::USER_ID . '/ingredients')
        ;

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testGetIndexAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()
            ->get('/users/' . UserSeeder::USER_ID . '/ingredients')
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostCreateAsOwnerReturnsCreated(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->post('/users/' . UserSeeder::USER_ID . '/ingredients', $this->validCreateBody())
        ;

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'ingredient' => $this->ingredientStructure(),
        ]);
        $response->assertJsonFragment([
            'name' => 'Basilic',
            'unit' => 'g',
        ]);

        $this->assertDatabaseHas('ingredients', [
            'name' => 'Basilic',
            'measurement_unit' => 'g',
            'user_id' => UserSeeder::USER_ID,
        ]);
    }

    public function testPostCreateAsNotOwnerReturnsForbidden(): void
    {
        $response = $this->getLoggedClient()
            ->post('/users/' . UserSeeder::USER_ID . '/ingredients', $this->validCreateBody())
        ;

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPostCreateAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()
            ->post('/users/' . UserSeeder::USER_ID . '/ingredients', $this->validCreateBody())
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostCreateWithEmptyBodyReturnsUnprocessableEntity(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->post('/users/' . UserSeeder::USER_ID . '/ingredients', [])
        ;

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['message' => 'Validation error']);
    }

    /** @return array<int, string> */
    private function ingredientStructure(): array
    {
        return ['id', 'name', 'unit'];
    }

    /** @return array<string, mixed> */
    private function validCreateBody(): array
    {
        return [
            'name' => 'Basilic',
            'unit' => 'g',
        ];
    }
}
