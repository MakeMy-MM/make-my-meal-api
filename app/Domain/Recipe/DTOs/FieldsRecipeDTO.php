<?php

namespace App\Domain\Recipe\DTOs;

use App\Domain\Recipe\Enums\RecipeType;
use App\DTOs\BaseFieldDTO;

class FieldsRecipeDTO extends BaseFieldDTO
{
    public function __construct(
        protected readonly ?string $name = null,
        protected readonly ?RecipeType $type = null,
        protected readonly ?string $image = null,
        protected readonly ?string $userId = null,
    ) {}

    /** @return array<string, mixed> */
    protected function getProperties(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'image' => $this->image,
            'user_id' => $this->userId,
        ];
    }
}
