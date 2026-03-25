<?php

namespace App\Domain\Ingredient\Repositories;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\DTOs\UpdateIngredientDTO;
use App\Domain\Ingredient\Models\Ingredient;
use App\DTOs\BaseFieldDTO;
use App\Repositories\ModelRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends ModelRepository<Ingredient>
 *
 * @method Collection<int, Ingredient> findByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 * @method Ingredient|null              findFirstByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 */
class IngredientRepository extends ModelRepository
{
    public function __construct(Ingredient $model)
    {
        parent::__construct($model);
    }

    public function create(CreateIngredientDTO $dto): Ingredient
    {
        return Ingredient::create($dto->toArray());
    }

    public function update(UpdateIngredientDTO $dto): Ingredient
    {
        $ingredient = $dto->getModel();
        $ingredient->fill($dto->toArray());
        $ingredient->save();

        return $ingredient;
    }

    public function delete(Ingredient $ingredient): void
    {
        $ingredient->delete();
    }
}
