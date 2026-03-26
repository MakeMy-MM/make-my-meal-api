<?php

namespace App\Domain\Recipe\DTOs;

use App\Domain\Recipe\Models\RecipeIngredient;
use App\DTOs\UpdateDTOInterface;

class UpdateRecipeIngredientDTO extends FieldsRecipeIngredientDTO implements UpdateDTOInterface
{
    public function __construct(
        private readonly RecipeIngredient $model,
        ?int $position = null,
        ?string $ingredientId = null,
        ?float $quantity = null,
    ) {
        parent::__construct(
            position: $position,
            ingredientId: $ingredientId,
            quantity: $quantity,
        );
    }

    public function getModel(): RecipeIngredient
    {
        return $this->model;
    }
}
