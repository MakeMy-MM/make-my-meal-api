<?php

namespace App\Domain\Recipe\DTOs;

use App\DTOs\BaseFieldDTO;

class FieldsRecipeStepDTO extends BaseFieldDTO
{
    public function __construct(
        protected readonly ?int $position = null,
        protected readonly ?string $description = null,
        protected readonly ?string $recipeId = null,
    ) {}

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /** @return array<string, mixed> */
    protected function getProperties(): array
    {
        return [
            'position' => $this->position,
            'description' => $this->description,
            'recipe_id' => $this->recipeId,
        ];
    }
}
