<?php

namespace Tests\Unit\Domain\Auth\Services;

use App\Domain\Auth\Models\RefreshToken;
use App\Domain\Auth\Services\LogoutService;
use App\Domain\Auth\Services\LogoutServiceInterface;
use App\Domain\Auth\Services\TokenServiceInterface;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\AccessToken;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class LogoutServiceTest extends TestUnitCase
{
    public function testLogoutRevokesAccessTokenAndDeletesRefreshToken(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $refreshTokenModel = $this->getRefreshToken();
        $accessToken = $this->getAccessToken();

        $tokenService = $this->getTokenService();
        $tokenService
            ->expects($this->once())
            ->method('deleteRefreshToken')
            ->with($refreshTokenModel)
        ;

        $accessToken
            ->expects($this->once())
            ->method('revoke')
            ->willReturn(true)
        ;

        $service = $this->getLogoutService($tokenService);
        $service->logout($accessToken, $refreshTokenModel);
    }

    public function testLogoutRollsBackOnException(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        $refreshTokenModel = $this->getRefreshToken();
        $accessToken = $this->getAccessToken();

        $tokenService = $this->getTokenService();

        $accessToken
            ->expects($this->once())
            ->method('revoke')
            ->willThrowException(new \RuntimeException('DB error'))
        ;

        $this->expectException(\RuntimeException::class);

        $service = $this->getLogoutService($tokenService);
        $service->logout($accessToken, $refreshTokenModel);
    }

    private function getLogoutService(
        ?TokenServiceInterface $tokenService = null,
    ): LogoutServiceInterface {
        return new LogoutService(
            $tokenService ?? $this->createStub(TokenServiceInterface::class),
        );
    }

    private function getTokenService(): TokenServiceInterface&MockObject
    {
        return $this->createMock(TokenServiceInterface::class);
    }

    private function getRefreshToken(
        string $id = 'fake-uuid',
    ): RefreshToken&MockObject {
        return $this->createConfiguredModelMock(RefreshToken::class, [
            'id' => $id,
        ]);
    }

    /** @return AccessToken<mixed>&MockObject */
    private function getAccessToken(): AccessToken&MockObject
    {
        return $this->createMock(AccessToken::class);
    }
}
