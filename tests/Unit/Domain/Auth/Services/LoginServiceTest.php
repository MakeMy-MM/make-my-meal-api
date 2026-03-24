<?php

namespace Tests\Unit\Domain\Auth\Services;

use App\Domain\Auth\DTOs\LoginDTO;
use App\Domain\Auth\Services\LoginService;
use App\Domain\Auth\Services\LoginServiceInterface;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use App\Http\Exceptions\UnauthorizedHttpException;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class LoginServiceTest extends TestUnitCase
{
    public function testLoginReturnsUser(): void
    {
        Hash::shouldReceive('check')->once()->with('Password1', 'hashed-password')->andReturnTrue();

        $dto = $this->getLoginDTO();
        $user = $this->getUser(password: 'hashed-password');
        $userRepository = $this->getUserRepository();

        $userRepository
            ->expects($this->once())
            ->method('findFirstByFields')
            ->willReturn($user)
        ;

        $service = $this->getLoginService($userRepository);
        $result = $service->login($dto);

        $this->assertSame($user, $result);
    }

    public function testLoginThrowsUnauthorizedWhenUserNotFound(): void
    {
        $dto = $this->getLoginDTO();
        $userRepository = $this->getUserRepository();

        $userRepository
            ->expects($this->once())
            ->method('findFirstByFields')
            ->willReturn(null)
        ;

        $this->expectException(UnauthorizedHttpException::class);

        $service = $this->getLoginService($userRepository);
        $service->login($dto);
    }

    public function testLoginThrowsUnauthorizedWhenPasswordInvalid(): void
    {
        Hash::shouldReceive('check')->once()->andReturnFalse();

        $dto = $this->getLoginDTO();
        $user = $this->getUser(password: 'hashed-password');
        $userRepository = $this->getUserRepository();

        $userRepository
            ->expects($this->once())
            ->method('findFirstByFields')
            ->willReturn($user)
        ;

        $this->expectException(UnauthorizedHttpException::class);

        $service = $this->getLoginService($userRepository);
        $service->login($dto);
    }

    private function getLoginService(
        ?UserRepository $userRepository = null,
    ): LoginServiceInterface {
        return new LoginService(
            $userRepository ?? $this->createStub(UserRepository::class),
        );
    }

    private function getUserRepository(): UserRepository&MockObject
    {
        return $this->createMock(UserRepository::class);
    }

    private function getLoginDTO(
        string $email = 'user@example.com',
        string $password = 'Password1',
    ): LoginDTO&MockObject {
        $mock = $this->createMock(LoginDTO::class);
        $mock->method('getEmail')->willReturn($email);
        $mock->method('getPassword')->willReturn($password);

        return $mock;
    }

    private function getUser(
        string $id = 'fake-uuid',
        string $email = 'user@example.com',
        string $password = 'hashed-password',
    ): User&MockObject {
        return $this->createConfiguredModelMock(User::class, [
            'id' => $id,
            'email' => $email,
            'password' => $password,
        ]);
    }
}
