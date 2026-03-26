<?php

namespace App\Domain\Recipe\DTOs;

use App\Domain\Recipe\Enums\RecipeType;
use App\Domain\Recipe\Models\Recipe;
use App\DTOs\UpdateDTOInterface;

class UpdateRecipeDTO extends FieldsRecipeDTO implements UpdateDTOInterface
{
    /**
     * @param array<int, UpdateRecipeStepItemDTO>|null $steps
     * @param array<int, UpdateRecipeIngredientItemDTO>|null $ingredients
     */
    public function __construct(
        private readonly Recipe $model,
        ?string $name = null,
        ?RecipeType $type = null,
        private readonly ?array $steps = null,
        private readonly ?array $ingredients = null,
    ) {
        parent::__construct(
            name: $name,
            type: $type,
        );
    }

    public function getModel(): Recipe
    {
        return $this->model;
    }

    /** @return array<int, UpdateRecipeStepItemDTO>|null */
    public function getSteps(): ?array
    {
        return $this->steps;
    }

    /** @return array<int, UpdateRecipeIngredientItemDTO>|null */
    public function getIngredients(): ?array
    {
        return $this->ingredients;
    }

    public function hasSteps(): bool
    {
        return $this->steps !== null;
    }

    public function hasIngredients(): bool
    {
        return $this->ingredients !== null;
    }
}
