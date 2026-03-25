<?php

namespace Tests\Unit\Domain\Recipe\Inputs;

use App\Domain\Recipe\Enums\RecipeType;
use App\Domain\Recipe\Http\Requests\CreateRecipeRequest;
use App\Domain\Recipe\Inputs\CreateRecipeInput;
use App\Domain\User\Models\User;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class CreateRecipeInputTest extends TestUnitCase
{
    public function testFromRequestReturnsCorrectToArray(): void
    {
        $input = CreateRecipeInput::fromRequest(
            $this->getRequest(),
            ['user' => $this->getUser()],
        );

        $this->assertSame([
            'name' => 'Whiskey Sour',
            'type' => RecipeType::Cocktail,
            'user_id' => 'user-uuid',
        ], $input->toArray());
    }

    public function testFromRequestMapsStepsWithPositions(): void
    {
        $input = CreateRecipeInput::fromRequest(
            $this->getRequest(steps: [
                ['description' => 'Pour whiskey'],
                ['description' => 'Add lemon juice'],
            ]),
            ['user' => $this->getUser()],
        );

        $steps = $input->getSteps();

        $this->assertCount(2, $steps);
        $this->assertSame(1, $steps[0]->getPosition());
        $this->assertSame('Pour whiskey', $steps[0]->getDescription());
        $this->assertSame(2, $steps[1]->getPosition());
        $this->assertSame('Add lemon juice', $steps[1]->getDescription());
    }

    public function testFromRequestMapsIngredientsWithPositions(): void
    {
        $input = CreateRecipeInput::fromRequest(
            $this->getRequest(ingredients: [
                ['id' => 'ingredient-1', 'quantity' => 1.5],
                ['id' => 'ingredient-2', 'quantity' => 2.0],
            ]),
            ['user' => $this->getUser()],
        );

        $ingredients = $input->getIngredients();

        $this->assertCount(2, $ingredients);
        $this->assertSame(1, $ingredients[0]->getPosition());
        $this->assertSame('ingredient-1', $ingredients[0]->getIngredientId());
        $this->assertSame(1.5, $ingredients[0]->getQuantity());
        $this->assertSame(2, $ingredients[1]->getPosition());
        $this->assertSame('ingredient-2', $ingredients[1]->getIngredientId());
        $this->assertSame(2.0, $ingredients[1]->getQuantity());
    }

    /**
     * @param array<int, array<string, string>> $steps
     * @param array<int, array<string, mixed>> $ingredients
     */
    private function getRequest(
        string $name = 'Whiskey Sour',
        string $type = 'cocktail',
        array $steps = [],
        array $ingredients = [],
    ): CreateRecipeRequest {
        return $this->createFormRequestMock(CreateRecipeRequest::class, [
            'name' => $name,
            'type' => $type,
            'steps' => $steps,
            'ingredients' => $ingredients,
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
