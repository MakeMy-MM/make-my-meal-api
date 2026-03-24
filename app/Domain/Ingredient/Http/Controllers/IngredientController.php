<?php

namespace App\Domain\Ingredient\Http\Controllers;

use App\Domain\Ingredient\Http\Requests\CreateIngredientRequest;
use App\Domain\Ingredient\Http\Resources\IngredientResource;
use App\Domain\Ingredient\Inputs\CreateIngredientInput;
use App\Domain\Ingredient\Services\IngredientServiceInterface;
use App\Domain\User\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IngredientController
{
    public function __construct(
        private readonly IngredientServiceInterface $ingredientService,
    ) {}

    public function create(CreateIngredientRequest $request, User $user): JsonResponse
    {
        $input = CreateIngredientInput::fromRequest($request, ['user' => $user]);
        $ingredient = $this->ingredientService->create($input);

        return (new IngredientResource($ingredient))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
        ;
    }
}
