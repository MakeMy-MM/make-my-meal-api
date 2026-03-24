<?php

namespace Tests\Unit\Domain\Auth\Services;

use App\Domain\Auth\DTOs\RegisterDTO;
use App\Domain\Auth\Services\RegisterService;
use App\Domain\Auth\Services\RegisterServiceInterface;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepository;
use App\Http\Exceptions\InternalServerErrorHttpException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class RegisterServiceTest extends TestUnitCase
{
    public function testRegisterReturnsUser(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $dto = $this->getRegisterDTO();
        $user = $this->getUser();
        $userRepository = $this->getUserRepository();

        $userRepository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($user)
        ;

        $service = $this->getRegisterService($userRepository);
        $result = $service->register($dto);

        $this->assertSame($user, $result);
    }

    public function testRegisterThrowsInternalServerErrorHttpException(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        $dto = $this->getRegisterDTO();
        $userRepository = $this->getUserRepository();

        $userRepository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new InternalServerErrorHttpException())
        ;

        $this->expectException(InternalServerErrorHttpException::class);

        $service = $this->getRegisterService($userRepository);
        $service->register($dto);
    }

    private function getRegisterService(
        ?UserRepository $userRepository = null,
    ): RegisterServiceInterface {
        return new RegisterService(
            $userRepository ?? $this->createStub(UserRepository::class),
        );
    }

    private function getUserRepository(): UserRepository&MockObject
    {
        return $this->createMock(UserRepository::class);
    }

    private function getRegisterDTO(): RegisterDTO&MockObject
    {
        return $this->createMock(RegisterDTO::class);
    }

    private function getUser(
        string $id = 'fake-uuid',
        string $email = 'user@example.com',
    ): User&MockObject {
        return $this->createConfiguredModelMock(User::class, [
            'id' => $id,
            'email' => $email,
        ]);
    }
}
