<?php

namespace App\Domain\Recipe\DTOs;

use App\DTOs\BaseFieldDTO;

class FieldsRecipeIngredientDTO extends BaseFieldDTO
{
    public function __construct(
        protected readonly ?int $position = null,
        protected readonly ?float $quantity = null,
        protected readonly ?string $ingredientId = null,
        protected readonly ?string $recipeId = null,
    ) {}

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function getIngredientId(): ?string
    {
        return $this->ingredientId;
    }

    /** @return array<string, mixed> */
    protected function getProperties(): array
    {
        return [
            'position' => $this->position,
            'quantity' => $this->quantity,
            'ingredient_id' => $this->ingredientId,
            'recipe_id' => $this->recipeId,
        ];
    }
}
