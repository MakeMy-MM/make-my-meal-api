<?php

namespace Tests\Unit\Domain\Ingredient\Inputs;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Ingredient\Http\Requests\UpdateIngredientRequest;
use App\Domain\Ingredient\Inputs\UpdateIngredientInput;
use App\Domain\Ingredient\Models\Ingredient;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class UpdateIngredientInputTest extends TestUnitCase
{
    public function testFromRequestWithAllFieldsReturnsCorrectToArray(): void
    {
        $input = UpdateIngredientInput::fromRequest(
            $this->getRequest(['name' => 'Updated', 'unit' => 'ml']),
            ['ingredient' => $this->getIngredient()],
        );

        $this->assertSame([
            'name' => 'Updated',
            'measurement_unit' => MeasurementUnit::MILLILITER,
        ], $input->toArray());
    }

    public function testFromRequestWithPartialFieldsFiltersNulls(): void
    {
        $input = UpdateIngredientInput::fromRequest(
            $this->getRequest(['name' => 'Updated']),
            ['ingredient' => $this->getIngredient()],
        );

        $this->assertSame([
            'name' => 'Updated',
        ], $input->toArray());
    }

    public function testFromRequestWithEmptyFieldsReturnsEmpty(): void
    {
        $input = UpdateIngredientInput::fromRequest(
            $this->getRequest([]),
            ['ingredient' => $this->getIngredient()],
        );

        $this->assertSame([], $input->toArray());
    }

    public function testFromRequestReturnsCorrectModel(): void
    {
        $ingredient = $this->getIngredient();
        $input = UpdateIngredientInput::fromRequest(
            $this->getRequest([]),
            ['ingredient' => $ingredient],
        );

        $this->assertSame($ingredient, $input->getModel());
    }

    /** @param array<string, mixed> $validated */
    private function getRequest(array $validated = []): UpdateIngredientRequest
    {
        return $this->createFormRequestMock(UpdateIngredientRequest::class, $validated);
    }

    private function getIngredient(
        string $id = 'ingredient-uuid',
    ): Ingredient&MockObject {
        return $this->createConfiguredModelMock(Ingredient::class, [
            'id' => $id,
        ]);
    }
}
