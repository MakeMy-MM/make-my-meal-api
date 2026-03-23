<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class TestFeatureCase extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private string $prefix = '/api/v1';

    /** @param array<string, string> $headers */
    public function get($uri, array $headers = []): TestResponse
    {
        return parent::getJson($this->prefix . $uri, $headers);
    }

    /** @param array<string, mixed> $data */
    public function post($uri, array $data = [], array $headers = []): TestResponse
    {
        return parent::postJson($this->prefix . $uri, $data, $headers);
    }

    /** @param array<string, mixed> $data */
    public function patch($uri, array $data = [], array $headers = []): TestResponse
    {
        return parent::patchJson($this->prefix . $uri, $data, $headers);
    }

    /** @param array<string, mixed> $data */
    public function put($uri, array $data = [], array $headers = []): TestResponse
    {
        return parent::putJson($this->prefix . $uri, $data, $headers);
    }

    /** @param array<string, mixed> $data */
    public function delete($uri, array $data = [], array $headers = []): TestResponse
    {
        return parent::deleteJson($this->prefix . $uri, $data, $headers);
    }
}
