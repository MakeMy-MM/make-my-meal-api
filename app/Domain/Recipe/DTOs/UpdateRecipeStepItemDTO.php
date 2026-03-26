<?php

namespace App\Domain\Recipe\DTOs;

class UpdateRecipeStepItemDTO extends FieldsRecipeStepDTO
{
    public function __construct(
        private readonly ?string $id = null,
        ?int $position = null,
        ?string $description = null,
    ) {
        parent::__construct(
            position: $position,
            description: $description,
        );
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
