<?php

namespace App\Domain\Recipe\DTOs;

use App\Domain\Recipe\Models\RecipeStep;
use App\DTOs\UpdateDTOInterface;

class UpdateRecipeStepDTO extends FieldsRecipeStepDTO implements UpdateDTOInterface
{
    public function __construct(
        private readonly RecipeStep $model,
        ?int $position = null,
        ?string $description = null,
    ) {
        parent::__construct(
            position: $position,
            description: $description,
        );
    }

    public function getModel(): RecipeStep
    {
        return $this->model;
    }
}
