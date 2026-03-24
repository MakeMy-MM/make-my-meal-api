<?php

namespace App\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\Enums\MeasurementUnit;

class CreateIngredientDTO extends FieldsIngredientDTO
{
    public function __construct(
        string $name,
        MeasurementUnit $measurementUnit,
        string $userId,
    ) {
        parent::__construct(
            name: $name,
            measurementUnit: $measurementUnit,
            userId: $userId,
        );
    }
}
