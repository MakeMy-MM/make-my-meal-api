<?php

namespace App\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\DTOs\BaseFieldDTO;

class FieldsIngredientDTO extends BaseFieldDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $image = null,
        public readonly ?MeasurementUnit $measurementUnit = null,
        public readonly ?string $userId = null,
    ) {}

    /** @return array<string, mixed> */
    protected function getProperties(): array
    {
        return [
            'name' => $this->name,
            'image' => $this->image,
            'measurement_unit' => $this->measurementUnit?->value,
            'user_id' => $this->userId,
        ];
    }
}
