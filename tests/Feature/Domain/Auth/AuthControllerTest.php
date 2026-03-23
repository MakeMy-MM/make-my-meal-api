<?php

namespace Tests\Feature\Domain\Auth;

use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class AuthControllerTest extends TestFeatureCase
{
    // --- REGISTER ---

    public function testPostRegisterReturnsCreated(): void
    {
        $response = $this->post('/auth/register', $this->validPostRegisterPayload());

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'email',
                'email_verified',
            ],
            'token',
        ]);
        $response->assertJsonFragment([
            'email' => 'register@example.com',
            'email_verified' => false,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'register@example.com',
        ]);
    }

    public function testPostRegisterReturnsUnprocessableEntity(): void
    {
        $response = $this->post('/auth/register', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['message' => 'Validation error']);
    }

    // --- LOGIN ---

    public function testPostLoginReturnsSuccess(): void
    {
        $response = $this->post('/auth/login', $this->validPostLoginPayload());

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'user' => [
                'id',
                'email',
                'email_verified',
            ],
            'token',
        ]);
        $response->assertJsonFragment([
            'email' => 'user@example.com',
        ]);
    }

    public function testPostLoginReturnsUnauthorizedWhenBodyEmpty(): void
    {
        $response = $this->post('/auth/login', []);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    // --- Helpers ---

    /** @return array<string, mixed> */
    private function validPostRegisterPayload(): array
    {
        return [
            'email' => 'register@example.com',
            'password' => 'Password1',
        ];
    }

    /** @return array<string, mixed> */
    private function validPostLoginPayload(): array
    {
        return [
            'email' => 'user@example.com',
            'password' => 'password',
        ];
    }
}
