<?php

namespace App\Domain\Recipe\DTOs;

use App\Domain\Recipe\Enums\RecipeType;

class CreateRecipeDTO extends FieldsRecipeDTO
{
    /**
     * @param array<int, FieldsRecipeStepDTO> $steps
     * @param array<int, FieldsRecipeIngredientDTO> $ingredients
     */
    public function __construct(
        string $name,
        RecipeType $type,
        string $userId,
        private readonly array $steps = [],
        private readonly array $ingredients = [],
    ) {
        parent::__construct(
            name: $name,
            type: $type,
            userId: $userId,
        );
    }

    /** @return array<int, FieldsRecipeStepDTO> */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /** @return array<int, FieldsRecipeIngredientDTO> */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }
}
