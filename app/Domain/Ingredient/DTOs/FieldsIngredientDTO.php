<?php

namespace App\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\User\Models\User;
use App\DTOs\BaseFieldDTO;

class FieldsIngredientDTO extends BaseFieldDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $image = null,
        public readonly ?MeasurementUnit $measurementUnit = null,
        public readonly ?User $user = null,
    ) {}

    /** @return array<string, mixed> */
    protected function getProperties(): array
    {
        return [
            'name' => $this->name,
            'image' => $this->image,
            'measurement_unit' => $this->measurementUnit,
            'user_id' => $this->user?->id,
        ];
    }
}
