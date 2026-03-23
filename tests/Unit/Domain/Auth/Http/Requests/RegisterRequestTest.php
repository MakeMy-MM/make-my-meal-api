<?php

namespace Tests\Unit\Domain\Auth\Http\Requests;

use App\Domain\Auth\Http\Requests\RegisterRequest;
use Tests\Unit\TestUnitCase;

class RegisterRequestTest extends TestUnitCase
{
    public function testRulesContainsEmailAndPassword(): void
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
    }

    public function testRulesEmailIsRequired(): void
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['email'][0]);
    }

    public function testRulesEmailContainsUniqueRule(): void
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertContains('email', $rules['email']);
        $this->assertContains('string', $rules['email']);
        $this->assertContains('max:255', $rules['email']);
    }

    public function testRulesPasswordIsRequired(): void
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['password'][0]);
    }

    public function testRulesPasswordContainsMinAndRegex(): void
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $this->assertContains('string', $rules['password']);
        $this->assertContains('min:8', $rules['password']);
        $this->assertContains('regex:/[a-z]/', $rules['password']);
        $this->assertContains('regex:/[A-Z]/', $rules['password']);
        $this->assertContains('regex:/[0-9]/', $rules['password']);
    }

    public function testMessagesContainsAllKeys(): void
    {
        $request = new RegisterRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('email.required', $messages);
        $this->assertArrayHasKey('email.email', $messages);
        $this->assertArrayHasKey('email.unique', $messages);
        $this->assertArrayHasKey('password.required', $messages);
        $this->assertArrayHasKey('password.min', $messages);
        $this->assertArrayHasKey('password.regex', $messages);
    }
}
