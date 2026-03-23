<?php

namespace App\Domain\Auth\Http\Controllers;

use App\Domain\Auth\Http\Requests\LoginRequest;
use App\Domain\Auth\Http\Requests\RegisterRequest;
use App\Domain\Auth\Inputs\LoginInput;
use App\Domain\Auth\Inputs\RegisterInput;
use App\Domain\Auth\Services\LoginServiceInterface;
use App\Domain\Auth\Services\RegisterServiceInterface;
use App\Domain\User\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    public function __construct(
        private readonly RegisterServiceInterface $registerService,
        private readonly LoginServiceInterface $loginService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $input = RegisterInput::fromRequest($request, []);

        $user = $this->registerService->register($input);

        $token = $user->createToken('auth-token');

        return (new UserResource($user))
            ->additional(['token' => $token->plainTextToken])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
        ;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $input = LoginInput::fromRequest($request, []);

        $user = $this->loginService->login($input);

        $token = $user->createToken('auth-token');

        return (new UserResource($user))
            ->additional(['token' => $token->plainTextToken])
            ->response()
            ->setStatusCode(Response::HTTP_OK)
        ;
    }
}
