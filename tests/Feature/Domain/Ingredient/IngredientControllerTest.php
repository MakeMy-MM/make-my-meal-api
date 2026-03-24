<?php

namespace Tests\Feature\Domain\Ingredient;

use Database\Seeders\IngredientSeeder;
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

    public function testPatchUpdateAsOwnerReturnsOk(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/ingredients/' . IngredientSeeder::TOMATE_ID, $this->validUpdateBody())
        ;

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'ingredient' => $this->ingredientStructure(),
        ]);
        $response->assertJsonFragment([
            'name' => 'Tomate cerise',
        ]);

        $this->assertDatabaseHas('ingredients', [
            'id' => IngredientSeeder::TOMATE_ID,
            'name' => 'Tomate cerise',
        ]);
    }

    public function testPatchUpdateAsNotOwnerReturnsForbidden(): void
    {
        $response = $this->getLoggedClient()
            ->patch('/users/' . UserSeeder::USER_ID . '/ingredients/' . IngredientSeeder::TOMATE_ID, $this->validUpdateBody())
        ;

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPatchUpdateAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()
            ->patch('/users/' . UserSeeder::USER_ID . '/ingredients/' . IngredientSeeder::TOMATE_ID, $this->validUpdateBody())
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPatchUpdateWithInvalidIdReturnsNotFound(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])
            ->patch('/users/' . UserSeeder::USER_ID . '/ingredients/00000000-0000-0000-0000-000000000000', $this->validUpdateBody())
        ;

        $response->assertStatus(Response::HTTP_NOT_FOUND);
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

    /** @return array<string, mixed> */
    private function validUpdateBody(): array
    {
        return [
            'name' => 'Tomate cerise',
        ];
    }
}
