<?php

namespace Tests\Unit\Domain\Recipe\Http\Requests;

use App\Domain\Recipe\Http\Requests\UpdateRecipeRequest;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\User\Models\User;
use Tests\Unit\TestUnitCase;

class UpdateRecipeRequestTest extends TestUnitCase
{
    public function testRulesContainsAllFields(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('type', $rules);
        $this->assertArrayHasKey('steps', $rules);
        $this->assertArrayHasKey('steps.*.id', $rules);
        $this->assertArrayHasKey('steps.*.description', $rules);
        $this->assertArrayHasKey('ingredients', $rules);
        $this->assertArrayHasKey('ingredients.*.id', $rules);
        $this->assertArrayHasKey('ingredients.*.ingredient_id', $rules);
        $this->assertArrayHasKey('ingredients.*.quantity', $rules);
    }

    public function testRulesNameIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('sometimes', $rules['name'][0]);
    }

    public function testRulesTypeIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('sometimes', $rules['type'][0]);
    }

    public function testRulesStepsIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('sometimes', $rules['steps'][0]);
    }

    public function testRulesStepIdIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('sometimes', $rules['steps.*.id'][0]);
    }

    public function testRulesStepIdContainsExistsWithRecipeScope(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertTrue($this->containsExistsRule($rules['steps.*.id']));
    }

    public function testRulesStepDescriptionIsRequiredWithoutId(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required_without:steps.*.id', $rules['steps.*.description'][0]);
    }

    public function testRulesIngredientsIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('sometimes', $rules['ingredients'][0]);
    }

    public function testRulesIngredientIdIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('sometimes', $rules['ingredients.*.id'][0]);
    }

    public function testRulesRecipeIngredientIdContainsExistsWithRecipeScope(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertTrue($this->containsExistsRule($rules['ingredients.*.id']));
    }

    public function testRulesIngredientRefIdIsRequiredWithoutId(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required_without:ingredients.*.id', $rules['ingredients.*.ingredient_id'][0]);
    }

    public function testRulesIngredientRefIdContainsExistsWithUserScope(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertTrue($this->containsExistsRule($rules['ingredients.*.ingredient_id']));
    }

    public function testRulesIngredientQuantityIsRequiredWithoutId(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required_without:ingredients.*.id', $rules['ingredients.*.quantity'][0]);
    }

    public function testMessagesContainsAllKeys(): void
    {
        $request = $this->getRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('name.string', $messages);
        $this->assertArrayHasKey('name.min', $messages);
        $this->assertArrayHasKey('name.max', $messages);
        $this->assertArrayHasKey('type.required', $messages);
        $this->assertArrayHasKey('steps.required', $messages);
        $this->assertArrayHasKey('steps.array', $messages);
        $this->assertArrayHasKey('steps.*.id.uuid', $messages);
        $this->assertArrayHasKey('steps.*.id.exists', $messages);
        $this->assertArrayHasKey('steps.*.description.required', $messages);
        $this->assertArrayHasKey('steps.*.description.required_without', $messages);
        $this->assertArrayHasKey('steps.*.description.string', $messages);
        $this->assertArrayHasKey('ingredients.required', $messages);
        $this->assertArrayHasKey('ingredients.array', $messages);
        $this->assertArrayHasKey('ingredients.*.id.uuid', $messages);
        $this->assertArrayHasKey('ingredients.*.id.exists', $messages);
        $this->assertArrayHasKey('ingredients.*.ingredient_id.required', $messages);
        $this->assertArrayHasKey('ingredients.*.ingredient_id.required_without', $messages);
        $this->assertArrayHasKey('ingredients.*.ingredient_id.uuid', $messages);
        $this->assertArrayHasKey('ingredients.*.ingredient_id.exists', $messages);
        $this->assertArrayHasKey('ingredients.*.quantity.required', $messages);
        $this->assertArrayHasKey('ingredients.*.quantity.required_without', $messages);
        $this->assertArrayHasKey('ingredients.*.quantity.numeric', $messages);
    }

    private function getRequest(
        ?User $user = null,
        ?Recipe $recipe = null,
    ): UpdateRecipeRequest {
        /** @var UpdateRecipeRequest */
        return $this->createRequestWithRouteParams(
            UpdateRecipeRequest::class,
            'PATCH',
            '/users/{user}/recipes/{recipe}',
            [
                'user' => $user ?? $this->createStub(User::class),
                'recipe' => $recipe ?? $this->createStub(Recipe::class),
            ],
        );
    }

}
