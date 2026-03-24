<?php

namespace Tests\Feature\Domain\User;

use Database\Seeders\UserSeeder;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class UserControllerTest extends TestFeatureCase
{
    public function testGetMeReturnsOk(): void
    {
        $response = $this->getLoggedClient(['email' => UserSeeder::USER_EMAIL])->get('/me');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'user' => ['id', 'email', 'email_verified'],
        ]);
        $response->assertJsonFragment([
            'email' => UserSeeder::USER_EMAIL,
        ]);
        $response->assertJsonMissingPath('tokens');
    }

    public function testGetMeAnonymouslyReturnsUnauthorized(): void
    {
        $response = $this->getClient()->get('/me');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
