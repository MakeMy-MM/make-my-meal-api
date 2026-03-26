<?php

namespace App\Domain\Recipe\DTOs;

class UpdateRecipeIngredientItemDTO extends FieldsRecipeIngredientDTO
{
    public function __construct(
        private readonly ?string $id = null,
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

    public function getId(): ?string
    {
        return $this->id;
    }
}
