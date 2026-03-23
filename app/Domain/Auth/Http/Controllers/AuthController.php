<?php

namespace App\Domain\Auth\Http\Controllers;

use App\Domain\Auth\Http\Requests\LoginRequest;
use App\Domain\Auth\Http\Requests\LogoutRequest;
use App\Domain\Auth\Http\Requests\RefreshRequest;
use App\Domain\Auth\Http\Requests\RegisterRequest;
use App\Domain\Auth\Http\Resources\TokenResource;
use App\Domain\Auth\Inputs\LoginInput;
use App\Domain\Auth\Inputs\RegisterInput;
use App\Domain\Auth\Services\LoginServiceInterface;
use App\Domain\Auth\Services\LogoutServiceInterface;
use App\Domain\Auth\Services\RegisterServiceInterface;
use App\Domain\Auth\Services\TokenServiceInterface;
use App\Domain\User\Http\Resources\UserResource;
use App\Domain\User\Models\User;
use App\Http\Exceptions\UnauthorizedHttpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    public function __construct(
        private readonly RegisterServiceInterface $registerService,
        private readonly LoginServiceInterface $loginService,
        private readonly LogoutServiceInterface $logoutService,
        private readonly TokenServiceInterface $tokenService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $input = RegisterInput::fromRequest($request, []);

        $user = $this->registerService->register($input);

        $tokenDTO = $this->tokenService->create($user);

        return (new UserResource($user))
            ->additional([TokenResource::$wrap => new TokenResource($tokenDTO)])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
        ;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $input = LoginInput::fromRequest($request, []);

        $user = $this->loginService->login($input);

        $tokenDTO = $this->tokenService->create($user);

        return (new UserResource($user))
            ->additional([TokenResource::$wrap => new TokenResource($tokenDTO)])
            ->response()
            ->setStatusCode(Response::HTTP_OK)
        ;
    }

    public function logout(LogoutRequest $request): HttpResponse
    {
        /** @var User $user */
        $user = $request->user();

        $accessToken = $user->token();

        if (!$accessToken instanceof \Laravel\Passport\AccessToken) {
            throw new UnauthorizedHttpException();
        }

        /** @var string $plainToken */
        $plainToken = $request->validated('refresh_token');

        $refreshToken = $this->tokenService->findValidRefreshToken($plainToken);

        if ($refreshToken === null || $refreshToken->user_id !== $user->id) {
            throw new UnauthorizedHttpException();
        }

        $this->logoutService->logout($accessToken, $refreshToken);

        return response()->noContent();
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_OK)
        ;
    }

    public function refresh(RefreshRequest $request): JsonResponse
    {
        /** @var string $plainToken */
        $plainToken = $request->validated('refresh_token');

        $refreshToken = $this->tokenService->findValidRefreshToken($plainToken);

        if ($refreshToken === null) {
            throw new UnauthorizedHttpException();
        }

        /** @var User $user */
        $user = $refreshToken->user;

        $this->tokenService->deleteRefreshToken($refreshToken);

        $tokenDTO = $this->tokenService->create($user);

        return (new TokenResource($tokenDTO))
            ->response()
            ->setStatusCode(Response::HTTP_OK)
        ;
    }
}
