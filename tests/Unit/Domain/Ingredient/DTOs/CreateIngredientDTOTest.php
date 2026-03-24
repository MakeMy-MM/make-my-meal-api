<?php

namespace Tests\Unit\Domain\Ingredient\DTOs;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\User\Models\User;
use Tests\Unit\TestUnitCase;

class CreateIngredientDTOTest extends TestUnitCase
{
    public function testToArrayReturnsAllFields(): void
    {
        $dto = $this->getCreateIngredientDTO();

        $this->assertSame([
            'name' => 'Tomate',
            'measurement_unit' => MeasurementUnit::KILOGRAM,
            'user_id' => 'fake-uuid',
        ], $dto->toArray());
    }

    private function getCreateIngredientDTO(
        string $name = 'Tomate',
        MeasurementUnit $measurementUnit = MeasurementUnit::KILOGRAM,
    ): CreateIngredientDTO {
        return new CreateIngredientDTO(
            name: $name,
            measurementUnit: $measurementUnit,
            user: $this->getUser(),
        );
    }

    private function getUser(
        string $id = 'fake-uuid',
    ): User&\PHPUnit\Framework\MockObject\MockObject {
        return $this->createConfiguredModelMock(User::class, [
            'id' => $id,
        ]);
    }
}
