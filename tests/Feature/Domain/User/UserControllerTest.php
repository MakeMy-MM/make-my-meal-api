<?php

namespace Tests\Feature\Domain\User;

use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class UserControllerTest extends TestFeatureCase
{
    public function testGetMeReturnsSuccess(): void
    {
        $response = $this->getLoggedClient(['email' => 'user@example.com'])->get('/me');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'user' => ['id', 'email', 'email_verified'],
        ]);
        $response->assertJsonFragment([
            'email' => 'user@example.com',
        ]);
        $response->assertJsonMissingPath('tokens');
    }

    public function testGetMeWithoutAccessTokenReturnsUnauthorized(): void
    {
        $response = $this->getClient()->get('/me');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
