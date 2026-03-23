<?php

namespace Tests\Unit\Domain\Auth\DTOs;

use App\Domain\Auth\DTOs\RegisterDTO;
use Tests\Unit\TestUnitCase;

class RegisterDTOTest extends TestUnitCase
{
    public function testToArrayReturnsEmailAndPassword(): void
    {
        $dto = $this->getRegisterDTO();

        $this->assertSame([
            'email' => 'user@example.com',
            'password' => 'Password1',
        ], $dto->toArray());
    }

    public function testGetEmailReturnsEmail(): void
    {
        $dto = $this->getRegisterDTO();

        $this->assertSame('user@example.com', $dto->getEmail());
    }

    public function testGetPasswordReturnsPassword(): void
    {
        $dto = $this->getRegisterDTO();

        $this->assertSame('Password1', $dto->getPassword());
    }

    private function getRegisterDTO(
        string $email = 'user@example.com',
        string $password = 'Password1',
    ): RegisterDTO {
        return new RegisterDTO(
            email: $email,
            password: $password,
        );
    }
}
