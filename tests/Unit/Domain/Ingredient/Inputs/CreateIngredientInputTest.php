<?php

namespace Tests\Unit\Domain\Ingredient\Inputs;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Http\Requests\CreateIngredientRequest;
use App\Domain\Ingredient\Inputs\CreateIngredientInput;
use App\Domain\User\Models\User;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class CreateIngredientInputTest extends TestUnitCase
{
    public function testFromRequestReturnsCorrectToArray(): void
    {
        $input = CreateIngredientInput::fromRequest(
            $this->getRequest(),
            ['user' => $this->getUser()],
        );

        $this->assertSame([
            'name' => 'Tomate',
            'measurement_unit' => MeasurementUnit::KILOGRAM,
            'user_id' => 'user-uuid',
        ], $input->toArray());
    }

    private function getRequest(
        string $name = 'Tomate',
        string $unit = 'kg',
    ): CreateIngredientRequest {
        return $this->createFormRequestMock(CreateIngredientRequest::class, [
            'name' => $name,
            'unit' => $unit,
        ]);
    }

    private function getUser(
        string $id = 'user-uuid',
    ): User&MockObject {
        return $this->createConfiguredModelMock(User::class, [
            'id' => $id,
        ]);
    }
}
