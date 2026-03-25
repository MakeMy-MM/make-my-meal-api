<?php

namespace App\Domain\Recipe\Http\Controllers;

use App\Domain\Recipe\Http\Requests\CreateRecipeRequest;
use App\Domain\Recipe\Http\Requests\DeleteRecipeRequest;
use App\Domain\Recipe\Http\Requests\IndexRecipeRequest;
use App\Domain\Recipe\Http\Resources\RecipeResource;
use App\Domain\Recipe\Http\Resources\RecipeResourceCollection;
use App\Domain\Recipe\Inputs\CreateRecipeInput;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Services\RecipeServiceInterface;
use App\Domain\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class RecipeController
{
    public function __construct(
        private readonly RecipeServiceInterface $recipeService,
    ) {}

    public function index(IndexRecipeRequest $request, User $user): JsonResponse
    {
        $recipes = $this->recipeService->getByUser($user);

        return (new RecipeResourceCollection($recipes))
            ->response()
            ->setStatusCode(Response::HTTP_OK)
        ;
    }

    public function create(CreateRecipeRequest $request, User $user): JsonResponse
    {
        $input = CreateRecipeInput::fromRequest($request, ['user' => $user]);
        $recipe = $this->recipeService->create($input);

        return (new RecipeResource($recipe))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
        ;
    }

    public function destroy(DeleteRecipeRequest $request, User $user, Recipe $recipe): HttpResponse
    {
        $this->recipeService->delete($recipe);

        return response()->noContent();
    }
}
