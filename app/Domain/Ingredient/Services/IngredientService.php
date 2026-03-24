<?php

namespace App\Domain\Ingredient\Services;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\DTOs\FieldsIngredientDTO;
use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\Ingredient\Repositories\IngredientRepository;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class IngredientService implements IngredientServiceInterface
{
    public function __construct(
        private readonly IngredientRepository $repository,
    ) {}

    public function create(CreateIngredientDTO $dto): Ingredient
    {
        DB::beginTransaction();

        try {
            $result = $this->repository->create($dto);
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        DB::commit();

        return $result;
    }

    /** @return Collection<int, Ingredient> */
    public function getByUser(User $user): Collection
    {
        return $this->repository->findByFields(new FieldsIngredientDTO(user: $user));
    }
}
