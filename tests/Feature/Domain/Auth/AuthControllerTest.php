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
            'user' => $this->userStructure(),
            'tokens' => $this->tokensStructure(),
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
            'user' => $this->userStructure(),
            'tokens' => $this->tokensStructure(),
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

    // --- REFRESH ---

    public function testPostRefreshReturnsSuccess(): void
    {
        $loginResponse = $this->post('/auth/login', $this->validPostLoginPayload());
        $refreshToken = $loginResponse->json('tokens.refresh_token.token');

        $response = $this->post('/auth/refresh', ['refresh_token' => $refreshToken]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'tokens' => $this->tokensStructure(),
        ]);
        $response->assertJsonMissingPath('user');

        $newRefreshToken = $response->json('tokens.refresh_token.token');
        $this->assertNotEquals($refreshToken, $newRefreshToken);
    }

    public function testPostRefreshReturnsUnauthorizedWhenTokenInvalid(): void
    {
        $response = $this->post('/auth/refresh', ['refresh_token' => 'invalid-token']);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostRefreshReturnsUnauthorizedWhenTokenAlreadyUsed(): void
    {
        $loginResponse = $this->post('/auth/login', $this->validPostLoginPayload());
        $refreshToken = $loginResponse->json('tokens.refresh_token.token');

        $this->post('/auth/refresh', ['refresh_token' => $refreshToken])
            ->assertStatus(Response::HTTP_OK)
        ;

        $response = $this->post('/auth/refresh', ['refresh_token' => $refreshToken]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostRefreshReturnsUnprocessableEntityWhenBodyEmpty(): void
    {
        $response = $this->post('/auth/refresh', []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // --- Helpers ---

    /** @return array<int, string> */
    private function userStructure(): array
    {
        return ['id', 'email', 'email_verified'];
    }

    /** @return array<string, array<int, string>> */
    private function tokensStructure(): array
    {
        return [
            'access_token' => ['token', 'type', 'expires_at'],
            'refresh_token' => ['token', 'expires_at'],
        ];
    }

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
