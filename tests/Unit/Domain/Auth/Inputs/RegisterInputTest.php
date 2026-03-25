<?php

namespace Tests\Unit\Domain\Auth\Inputs;

use App\Domain\Auth\Http\Requests\RegisterRequest;
use App\Domain\Auth\Inputs\RegisterInput;
use Tests\Unit\TestUnitCase;

class RegisterInputTest extends TestUnitCase
{
    public function testFromRequestReturnsCorrectToArray(): void
    {
        $input = RegisterInput::fromRequest($this->getRequest(), []);

        $this->assertSame([
            'email' => 'user@example.com',
            'password' => 'Password1',
        ], $input->toArray());
    }

    public function testFromRequestReturnsCorrectGetters(): void
    {
        $input = RegisterInput::fromRequest($this->getRequest(), []);

        $this->assertSame('user@example.com', $input->getEmail());
        $this->assertSame('Password1', $input->getPassword());
    }

    private function getRequest(
        string $email = 'user@example.com',
        string $password = 'Password1',
    ): RegisterRequest {
        return $this->createFormRequestMock(RegisterRequest::class, [
            'email' => $email,
            'password' => $password,
        ]);
    }
}
