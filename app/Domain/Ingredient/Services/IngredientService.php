<?php

namespace App\Domain\Ingredient\Services;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\DTOs\FieldsIngredientDTO;
use App\Domain\Ingredient\DTOs\UpdateIngredientDTO;
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
        return DB::transaction(fn() => $this->repository->create($dto));
    }

    public function update(UpdateIngredientDTO $dto): Ingredient
    {
        return DB::transaction(fn() => $this->repository->update($dto));
    }

    public function delete(Ingredient $ingredient): void
    {
        DB::transaction(fn() => $this->repository->delete($ingredient));
    }

    /** @return Collection<int, Ingredient> */
    public function getByUser(User $user): Collection
    {
        return $this->repository->findByFields(new FieldsIngredientDTO(userId: $user->id));
    }
}
