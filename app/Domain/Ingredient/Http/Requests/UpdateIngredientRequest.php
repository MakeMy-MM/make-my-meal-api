<?php

namespace App\Domain\Ingredient\Http\Requests;

use App\Domain\Ingredient\Enums\IngredientRequestRule;
use App\Http\Requests\RoleRequest;

class UpdateIngredientRequest extends RoleRequest
{
    public function authorize(): bool
    {
        return $this->isSelf();
    }

    public function rules(): array
    {
        return [
            IngredientRequestRule::NAME->value => $this->optionnalRules(IngredientRequestRule::NAME->rules()),
            IngredientRequestRule::UNIT->value => $this->optionnalRules(IngredientRequestRule::UNIT->rules()),
        ];
    }

    public function messages(): array
    {
        return array_merge(
            IngredientRequestRule::NAME->messages(),
            IngredientRequestRule::UNIT->messages(),
        );
    }
}
