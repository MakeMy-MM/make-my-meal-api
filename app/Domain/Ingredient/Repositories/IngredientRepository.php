<?php

namespace App\Domain\Ingredient\Repositories;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\Models\Ingredient;
use App\DTOs\BaseFieldDTO;
use App\Http\Exceptions\InternalServerErrorHttpException;
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
        try {
            return Ingredient::create($dto->toArray());
        } catch (\Throwable $e) {
            throw new InternalServerErrorHttpException(previous: $e);
        }
    }
}
