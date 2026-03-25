<?php

namespace App\Domain\Recipe\DTOs;

use Webmozart\Assert\Assert;

class CreateRecipeStepDTO extends FieldsRecipeStepDTO
{
    public function __construct(
        int $position,
        string $description,
        string $recipeId,
    ) {
        parent::__construct(
            position: $position,
            description: $description,
            recipeId: $recipeId,
        );
    }

    public static function fromFields(FieldsRecipeStepDTO $fields, string $recipeId): self
    {
        Assert::notNull($fields->getPosition());
        Assert::notNull($fields->getDescription());

        return new self(
            position: $fields->getPosition(),
            description: $fields->getDescription(),
            recipeId: $recipeId,
        );
    }
}
