<?php

namespace App\Domain\Recipe\DTOs;

use Webmozart\Assert\Assert;

class CreateRecipeIngredientDTO extends FieldsRecipeIngredientDTO
{
    public function __construct(
        int $position,
        string $ingredientId,
        float $quantity,
        string $recipeId,
    ) {
        parent::__construct(
            position: $position,
            ingredientId: $ingredientId,
            quantity: $quantity,
            recipeId: $recipeId,
        );
    }

    public static function fromFields(FieldsRecipeIngredientDTO $fields, string $recipeId): self
    {
        Assert::notNull($fields->getPosition());
        Assert::notNull($fields->getIngredientId());
        Assert::notNull($fields->getQuantity());

        return new self(
            position: $fields->getPosition(),
            ingredientId: $fields->getIngredientId(),
            quantity: $fields->getQuantity(),
            recipeId: $recipeId,
        );
    }
}
