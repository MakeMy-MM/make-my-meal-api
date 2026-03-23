<?php

namespace Tests\Feature;

use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class TestFeatureCase extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private string $prefix = '/api/v1';

    /** @var array<string, string> */
    private array $authHeaders = [];

    private bool $clientInitialized = false;

    protected function getClient(): static
    {
        $this->authHeaders = [];
        $this->clientInitialized = true;

        return $this;
    }

    /** @param array<string, mixed> $attributes */
    protected function getLoggedClient(array $attributes = []): static
    {
        $user = User::where('email', $attributes['email'] ?? null)->first();

        if ($user === null) {
            $user = User::factory()->create($attributes);
        }

        $accessToken = $user->createToken('access')->accessToken;
        $this->authHeaders = ['Authorization' => 'Bearer ' . $accessToken];
        $this->clientInitialized = true;

        return $this;
    }

    /** @param array<string, string> $headers */
    public function get($uri, array $headers = []): TestResponse
    {
        $this->assertClientInitialized();

        return parent::getJson($this->prefix . $uri, array_merge($this->authHeaders, $headers));
    }

    /** @param array<string, mixed> $data */
    public function post($uri, array $data = [], array $headers = []): TestResponse
    {
        $this->assertClientInitialized();

        return parent::postJson($this->prefix . $uri, $data, array_merge($this->authHeaders, $headers));
    }

    /** @param array<string, mixed> $data */
    public function patch($uri, array $data = [], array $headers = []): TestResponse
    {
        $this->assertClientInitialized();

        return parent::patchJson($this->prefix . $uri, $data, array_merge($this->authHeaders, $headers));
    }

    /** @param array<string, mixed> $data */
    public function put($uri, array $data = [], array $headers = []): TestResponse
    {
        $this->assertClientInitialized();

        return parent::putJson($this->prefix . $uri, $data, array_merge($this->authHeaders, $headers));
    }

    /** @param array<string, mixed> $data */
    public function delete($uri, array $data = [], array $headers = []): TestResponse
    {
        $this->assertClientInitialized();

        return parent::deleteJson($this->prefix . $uri, $data, array_merge($this->authHeaders, $headers));
    }

    private function assertClientInitialized(): void
    {
        if (!$this->clientInitialized) {
            throw new \LogicException('Use getClient() or getLoggedClient() before calling HTTP methods.');
        }

        $this->clientInitialized = false;
    }
}
