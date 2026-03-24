<?php

namespace App\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\User\Models\User;

class CreateIngredientDTO extends FieldsIngredientDTO
{
    public function __construct(
        string $name,
        MeasurementUnit $measurementUnit,
        User $user,
    ) {
        parent::__construct(
            name: $name,
            measurementUnit: $measurementUnit,
            user: $user,
        );
    }
}
