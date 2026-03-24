<?php

namespace App\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Models\Ingredient;
use App\DTOs\UpdateDTOInterface;

class UpdateIngredientDTO extends FieldsIngredientDTO implements UpdateDTOInterface
{
    public function __construct(
        private readonly Ingredient $model,
        ?string $name = null,
        ?MeasurementUnit $measurementUnit = null,
    ) {
        parent::__construct(
            name: $name,
            measurementUnit: $measurementUnit,
        );
    }

    public function getModel(): Ingredient
    {
        return $this->model;
    }
}
