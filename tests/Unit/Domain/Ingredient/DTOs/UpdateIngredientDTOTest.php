<?php

namespace Tests\Unit\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\DTOs\UpdateIngredientDTO;
use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Models\Ingredient;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class UpdateIngredientDTOTest extends TestUnitCase
{
    public function testToArrayFiltersNullValues(): void
    {
        $dto = $this->getUpdateIngredientDTO();

        $this->assertSame([], $dto->toArray());
    }

    public function testToArrayReturnsOnlySetFields(): void
    {
        $dto = $this->getUpdateIngredientDTO(name: 'Updated');

        $this->assertSame(['name' => 'Updated'], $dto->toArray());
    }

    public function testToArrayReturnsAllFields(): void
    {
        $dto = $this->getUpdateIngredientDTO(
            name: 'Updated',
            measurementUnit: MeasurementUnit::LITER,
        );

        $this->assertSame([
            'name' => 'Updated',
            'measurement_unit' => MeasurementUnit::LITER,
        ], $dto->toArray());
    }

    public function testGetModelReturnsModel(): void
    {
        $ingredient = $this->getIngredient();
        $dto = $this->getUpdateIngredientDTO(ingredient: $ingredient);

        $this->assertSame($ingredient, $dto->getModel());
    }

    private function getUpdateIngredientDTO(
        ?Ingredient $ingredient = null,
        ?string $name = null,
        ?MeasurementUnit $measurementUnit = null,
    ): UpdateIngredientDTO {
        return new UpdateIngredientDTO(
            model: $ingredient ?? $this->getIngredient(),
            name: $name,
            measurementUnit: $measurementUnit,
        );
    }

    private function getIngredient(
        string $id = 'fake-uuid',
    ): Ingredient&MockObject {
        return $this->createConfiguredModelMock(Ingredient::class, [
            'id' => $id,
        ]);
    }
}
