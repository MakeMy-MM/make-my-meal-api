<?php

namespace Tests\Unit\Domain\Recipe\Inputs;

use App\Domain\Recipe\Http\Requests\UpdateRecipeRequest;
use App\Domain\Recipe\Inputs\UpdateRecipeInput;
use App\Domain\Recipe\Models\Recipe;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class UpdateRecipeInputTest extends TestUnitCase
{
    public function testFromRequestWithAllFieldsReturnsCorrectToArray(): void
    {
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest(['name' => 'Updated', 'type' => 'main']),
            ['recipe' => $this->getRecipe()],
        );

        $this->assertSame([
            'name' => 'Updated',
            'type' => \App\Domain\Recipe\Enums\RecipeType::Main,
        ], $input->toArray());
    }

    public function testFromRequestWithEmptyFieldsReturnsEmpty(): void
    {
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest([]),
            ['recipe' => $this->getRecipe()],
        );

        $this->assertSame([], $input->toArray());
    }

    public function testFromRequestReturnsCorrectModel(): void
    {
        $recipe = $this->getRecipe();
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest([]),
            ['recipe' => $recipe],
        );

        $this->assertSame($recipe, $input->getModel());
    }

    public function testFromRequestWithoutStepsReturnsNullSteps(): void
    {
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest([]),
            ['recipe' => $this->getRecipe()],
        );

        $this->assertNull($input->getSteps());
        $this->assertFalse($input->hasSteps());
    }

    public function testFromRequestWithStepsMapsCorrectly(): void
    {
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest([
                'steps' => [
                    ['id' => 'step-1', 'description' => 'Updated step'],
                    ['description' => 'New step'],
                ],
            ]),
            ['recipe' => $this->getRecipe()],
        );

        $this->assertTrue($input->hasSteps());

        $steps = $input->getSteps();
        $this->assertNotNull($steps);
        $this->assertCount(2, $steps);

        $this->assertSame('step-1', $steps[0]->getId());
        $this->assertSame(1, $steps[0]->getPosition());
        $this->assertSame('Updated step', $steps[0]->getDescription());

        $this->assertNull($steps[1]->getId());
        $this->assertSame(2, $steps[1]->getPosition());
        $this->assertSame('New step', $steps[1]->getDescription());
    }

    public function testFromRequestWithoutIngredientsReturnsNullIngredients(): void
    {
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest([]),
            ['recipe' => $this->getRecipe()],
        );

        $this->assertNull($input->getIngredients());
        $this->assertFalse($input->hasIngredients());
    }

    public function testFromRequestWithIngredientsMapsCorrectly(): void
    {
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest([
                'ingredients' => [
                    ['id' => 'ri-1', 'ingredient_id' => 'ing-1', 'quantity' => 2.5],
                    ['ingredient_id' => 'ing-2', 'quantity' => 1.0],
                ],
            ]),
            ['recipe' => $this->getRecipe()],
        );

        $this->assertTrue($input->hasIngredients());

        $ingredients = $input->getIngredients();
        $this->assertNotNull($ingredients);
        $this->assertCount(2, $ingredients);

        $this->assertSame('ri-1', $ingredients[0]->getId());
        $this->assertSame(1, $ingredients[0]->getPosition());
        $this->assertSame('ing-1', $ingredients[0]->getIngredientId());
        $this->assertSame(2.5, $ingredients[0]->getQuantity());

        $this->assertNull($ingredients[1]->getId());
        $this->assertSame(2, $ingredients[1]->getPosition());
        $this->assertSame('ing-2', $ingredients[1]->getIngredientId());
        $this->assertSame(1.0, $ingredients[1]->getQuantity());
    }

    public function testFromRequestWithExistingIngredientWithoutOptionalFields(): void
    {
        $input = UpdateRecipeInput::fromRequest(
            $this->getRequest([
                'ingredients' => [
                    ['id' => 'ri-1'],
                ],
            ]),
            ['recipe' => $this->getRecipe()],
        );

        $ingredients = $input->getIngredients();
        $this->assertNotNull($ingredients);
        $this->assertSame('ri-1', $ingredients[0]->getId());
        $this->assertNull($ingredients[0]->getIngredientId());
        $this->assertNull($ingredients[0]->getQuantity());
    }

    /** @param array<string, mixed> $validated */
    private function getRequest(array $validated = []): UpdateRecipeRequest
    {
        return $this->createFormRequestMock(UpdateRecipeRequest::class, $validated);
    }

    private function getRecipe(
        string $id = 'recipe-uuid',
    ): Recipe&MockObject {
        return $this->createConfiguredModelMock(Recipe::class, [
            'id' => $id,
        ]);
    }
}
