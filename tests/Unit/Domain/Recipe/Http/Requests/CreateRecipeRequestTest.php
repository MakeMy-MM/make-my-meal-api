<?php

namespace Tests\Unit\Domain\Recipe\Http\Requests;

use App\Domain\Recipe\Http\Requests\CreateRecipeRequest;
use App\Domain\User\Models\User;
use Tests\Unit\TestUnitCase;

class CreateRecipeRequestTest extends TestUnitCase
{
    public function testRulesContainsAllFields(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('type', $rules);
        $this->assertArrayHasKey('steps', $rules);
        $this->assertArrayHasKey('steps.*.description', $rules);
        $this->assertArrayHasKey('ingredients', $rules);
        $this->assertArrayHasKey('ingredients.*.id', $rules);
        $this->assertArrayHasKey('ingredients.*.quantity', $rules);
    }

    public function testRulesNameIsRequired(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['name'][0]);
    }

    public function testRulesTypeIsRequired(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['type'][0]);
    }

    public function testRulesStepsIsRequired(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['steps'][0]);
    }

    public function testRulesStepDescriptionIsRequired(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['steps.*.description'][0]);
    }

    public function testRulesIngredientsIsRequired(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['ingredients'][0]);
    }

    public function testRulesIngredientIdIsRequired(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['ingredients.*.id'][0]);
    }

    public function testRulesIngredientIdContainsExistsWithUserScope(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertTrue($this->containsExistsRule($rules['ingredients.*.id']));
    }

    public function testRulesIngredientQuantityIsRequired(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['ingredients.*.quantity'][0]);
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
        $this->assertArrayHasKey('type', $messages);
        $this->assertArrayHasKey('steps.required', $messages);
        $this->assertArrayHasKey('steps.array', $messages);
        $this->assertArrayHasKey('steps.min', $messages);
        $this->assertArrayHasKey('steps.*.description.required', $messages);
        $this->assertArrayHasKey('steps.*.description.string', $messages);
        $this->assertArrayHasKey('ingredients.required', $messages);
        $this->assertArrayHasKey('ingredients.array', $messages);
        $this->assertArrayHasKey('ingredients.min', $messages);
        $this->assertArrayHasKey('ingredients.*.id.required', $messages);
        $this->assertArrayHasKey('ingredients.*.quantity.required', $messages);
    }

    private function getRequest(
        ?User $user = null,
    ): CreateRecipeRequest {
        /** @var CreateRecipeRequest */
        return $this->createRequestWithRouteParams(
            CreateRecipeRequest::class,
            'POST',
            '/users/{user}/recipes',
            ['user' => $user ?? $this->createStub(User::class)],
        );
    }

}
