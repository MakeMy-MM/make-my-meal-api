<?php

namespace Tests\Unit\Domain\Ingredient\Http\Requests;

use App\Domain\Ingredient\Http\Requests\CreateIngredientRequest;
use Tests\Unit\TestUnitCase;

class CreateIngredientRequestTest extends TestUnitCase
{
    public function testRulesContainsNameAndUnit(): void
    {
        $request = new CreateIngredientRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('unit', $rules);
    }

    public function testRulesNameIsRequired(): void
    {
        $request = new CreateIngredientRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['name'][0]);
    }

    public function testRulesNameContainsStringMinMax(): void
    {
        $request = new CreateIngredientRequest();
        $rules = $request->rules();

        $this->assertContains('string', $rules['name']);
        $this->assertContains('min:3', $rules['name']);
        $this->assertContains('max:63', $rules['name']);
    }

    public function testRulesUnitIsRequired(): void
    {
        $request = new CreateIngredientRequest();
        $rules = $request->rules();

        $this->assertSame('required', $rules['unit'][0]);
    }

    public function testRulesUnitContainsEnumRule(): void
    {
        $request = new CreateIngredientRequest();
        $rules = $request->rules();

        $this->assertInstanceOf(\Illuminate\Validation\Rules\Enum::class, $rules['unit'][1]);
    }

    public function testMessagesContainsAllKeys(): void
    {
        $request = new CreateIngredientRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('name.string', $messages);
        $this->assertArrayHasKey('name.min', $messages);
        $this->assertArrayHasKey('name.max', $messages);
        $this->assertArrayHasKey('unit.required', $messages);
        $this->assertArrayHasKey('unit', $messages);
    }
}
