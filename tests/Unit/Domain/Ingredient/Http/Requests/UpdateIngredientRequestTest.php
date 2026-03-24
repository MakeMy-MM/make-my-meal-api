<?php

namespace Tests\Unit\Domain\Ingredient\Http\Requests;

use App\Domain\Ingredient\Http\Requests\UpdateIngredientRequest;
use Tests\Unit\TestUnitCase;

class UpdateIngredientRequestTest extends TestUnitCase
{
    public function testRulesContainsNameAndUnit(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('unit', $rules);
    }

    public function testRulesNameIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertContains('sometimes', $rules['name']);
        $this->assertNotContains('required', $rules['name']);
    }

    public function testRulesUnitIsSometimes(): void
    {
        $request = $this->getRequest();
        $rules = $request->rules();

        $this->assertContains('sometimes', $rules['unit']);
        $this->assertNotContains('required', $rules['unit']);
    }

    public function testMessagesContainsAllKeys(): void
    {
        $request = $this->getRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('name.string', $messages);
        $this->assertArrayHasKey('name.min', $messages);
        $this->assertArrayHasKey('name.max', $messages);
        $this->assertArrayHasKey('unit.required', $messages);
        $this->assertArrayHasKey('unit', $messages);
    }

    private function getRequest(): UpdateIngredientRequest
    {
        return new UpdateIngredientRequest();
    }
}
