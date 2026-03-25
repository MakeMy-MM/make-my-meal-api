<?php

namespace App\Domain\Recipe\Http\Controllers;

use App\Domain\Recipe\Http\Requests\CreateRecipeRequest;
use App\Domain\Recipe\Http\Resources\RecipeResource;
use App\Domain\Recipe\Inputs\CreateRecipeInput;
use App\Domain\Recipe\Services\RecipeServiceInterface;
use App\Domain\User\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class RecipeController
{
    public function __construct(
        private readonly RecipeServiceInterface $recipeService,
    ) {}

    public function create(CreateRecipeRequest $request, User $user): JsonResponse
    {
        $input = CreateRecipeInput::fromRequest($request, ['user' => $user]);
        $recipe = $this->recipeService->create($input);

        return (new RecipeResource($recipe))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
        ;
    }
}
