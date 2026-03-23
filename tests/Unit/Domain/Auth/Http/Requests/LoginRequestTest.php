<?php

namespace Tests\Unit\Domain\Auth\Http\Requests;

use App\Domain\Auth\Http\Requests\LoginRequest;
use Tests\Unit\TestUnitCase;

class LoginRequestTest extends TestUnitCase
{
    public function testRulesContainsEmailAndPassword(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
    }

    public function testRulesEmailIsRequired(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['email'][0]);
        $this->assertContains('string', $rules['email']);
        $this->assertContains('email', $rules['email']);
    }

    public function testRulesPasswordIsRequired(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['password'][0]);
        $this->assertContains('string', $rules['password']);
    }

    public function testMessagesContainsAllKeys(): void
    {
        $request = new LoginRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('password.required', $messages);
    }
}
