<?php

namespace App\Domain\Recipe\Http\Requests;

use App\Domain\Ingredient\Enums\IngredientRequestRule;
use App\Domain\Recipe\Enums\RecipeRequestRule;
use App\Http\Requests\RoleRequest;

class CreateRecipeRequest extends RoleRequest
{
    public function authorize(): bool
    {
        return $this->isSelf();
    }

    public function rules(): array
    {
        return [
            RecipeRequestRule::NAME->value => $this->requiredRules(RecipeRequestRule::NAME->rules()),
            RecipeRequestRule::TYPE->value => $this->requiredRules(RecipeRequestRule::TYPE->rules()),
            RecipeRequestRule::STEPS->value => $this->requiredRules(RecipeRequestRule::STEPS->rules()),
            RecipeRequestRule::STEP_DESCRIPTION->value => $this->requiredRules(RecipeRequestRule::STEP_DESCRIPTION->rules()),
            RecipeRequestRule::INGREDIENTS->value => $this->requiredRules(RecipeRequestRule::INGREDIENTS->rules()),
            RecipeRequestRule::INGREDIENT . IngredientRequestRule::ID->value => $this->requiredRules(RecipeRequestRule::INGREDIENT_ID->rules()),
            RecipeRequestRule::INGREDIENT_QUANTITY->value => $this->requiredRules(RecipeRequestRule::INGREDIENT_QUANTITY->rules()),
        ];
    }

    public function messages(): array
    {
        return array_merge(
            RecipeRequestRule::NAME->messages(),
            RecipeRequestRule::TYPE->messages(),
            RecipeRequestRule::STEPS->messages(),
            RecipeRequestRule::STEP_DESCRIPTION->messages(),
            RecipeRequestRule::INGREDIENTS->messages(),
            IngredientRequestRule::ID->messages(RecipeRequestRule::INGREDIENT),
            RecipeRequestRule::INGREDIENT_QUANTITY->messages(),
        );
    }
}
