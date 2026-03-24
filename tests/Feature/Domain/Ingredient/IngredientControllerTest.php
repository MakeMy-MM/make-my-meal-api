<?php

namespace Tests\Feature\Domain\Ingredient;

use App\Domain\User\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class IngredientControllerTest extends TestFeatureCase
{
    public function testPostCreateReturnsCreated(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->getLoggedClient(['email' => $user->email])
            ->post("/users/{$user->id}/ingredients", $this->validCreateBody())
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
            'user_id' => $user->id,
        ]);
    }

    public function testPostCreateWithoutAccessTokenReturnsUnauthorized(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->getClient()
            ->post("/users/{$user->id}/ingredients", $this->validCreateBody())
        ;

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostCreateWithEmptyBodyReturnsUnprocessableEntity(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->getLoggedClient(['email' => $user->email])
            ->post("/users/{$user->id}/ingredients", [])
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
