<?php

namespace Tests\Unit\Domain\Auth\Http\Requests;

use App\Domain\Auth\Http\Requests\LogoutRequest;
use Tests\Unit\TestUnitCase;

class LogoutRequestTest extends TestUnitCase
{
    public function testRulesContainsRefreshToken(): void
    {
        $request = new LogoutRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('refresh_token', $rules);
    }

    public function testRulesRefreshTokenIsRequired(): void
    {
        $request = new LogoutRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['refresh_token'][0]);
        $this->assertContains('string', $rules['refresh_token']);
    }

    public function testMessagesContainsAllKeys(): void
    {
        $request = new LogoutRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('refresh_token.required', $messages);
        $this->assertArrayHasKey('refresh_token.string', $messages);
    }
}
