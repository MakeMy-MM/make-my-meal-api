<?php

namespace Tests\Unit\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\Enums\MeasurementUnit;
use Tests\Unit\TestUnitCase;

class CreateIngredientDTOTest extends TestUnitCase
{
    public function testToArrayReturnsAllFields(): void
    {
        $dto = $this->getCreateIngredientDTO();

        $this->assertSame([
            'name' => 'Tomate',
            'measurement_unit' => 'kg',
            'user_id' => 'fake-uuid',
        ], $dto->toArray());
    }

    private function getCreateIngredientDTO(
        string $name = 'Tomate',
        MeasurementUnit $measurementUnit = MeasurementUnit::KILOGRAM,
        string $userId = 'fake-uuid',
    ): CreateIngredientDTO {
        return new CreateIngredientDTO(
            name: $name,
            measurementUnit: $measurementUnit,
            userId: $userId,
        );
    }
}
