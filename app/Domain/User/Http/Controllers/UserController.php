<?php

namespace App\Domain\User\Http\Controllers;

use App\Domain\User\Http\Resources\UserResource;
use App\Domain\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_OK)
        ;
    }
}
