<?php

namespace App\Domain\Recipe\Http\Requests;

use App\Domain\Recipe\Enums\RecipeRequestRule;
use App\Http\Requests\RoleRequest;

class UpdateRecipeRequest extends RoleRequest
{
    public function authorize(): bool
    {
        return $this->isSelf();
    }

    public function rules(): array
    {
        return [
            RecipeRequestRule::NAME->value => $this->optionnalRules(RecipeRequestRule::NAME->rules()),
            RecipeRequestRule::TYPE->value => $this->optionnalRules(RecipeRequestRule::TYPE->rules()),
            RecipeRequestRule::STEPS->value => $this->optionnalRules(RecipeRequestRule::STEPS->rules()),
            RecipeRequestRule::STEP_ID->value => $this->optionnalRules(RecipeRequestRule::STEP_ID->rules()),
            RecipeRequestRule::STEP_DESCRIPTION->value => $this->requiredWithoutRules(
                RecipeRequestRule::STEP_ID->value,
                RecipeRequestRule::STEP_DESCRIPTION->rules(),
            ),
            RecipeRequestRule::INGREDIENTS->value => $this->optionnalRules(RecipeRequestRule::INGREDIENTS->rules()),
            RecipeRequestRule::RECIPE_INGREDIENT_ID->value => $this->optionnalRules(RecipeRequestRule::RECIPE_INGREDIENT_ID->rules()),
            RecipeRequestRule::INGREDIENT_ID->value => $this->requiredWithoutRules(
                RecipeRequestRule::RECIPE_INGREDIENT_ID->value,
                RecipeRequestRule::INGREDIENT_ID->rules(),
            ),
            RecipeRequestRule::INGREDIENT_QUANTITY->value => $this->requiredWithoutRules(
                RecipeRequestRule::RECIPE_INGREDIENT_ID->value,
                RecipeRequestRule::INGREDIENT_QUANTITY->rules(),
            ),
        ];
    }

    public function messages(): array
    {
        return array_merge(
            RecipeRequestRule::NAME->messages(),
            RecipeRequestRule::TYPE->messages(),
            RecipeRequestRule::STEPS->messages(),
            RecipeRequestRule::STEP_ID->messages(),
            RecipeRequestRule::STEP_DESCRIPTION->messages(),
            RecipeRequestRule::INGREDIENTS->messages(),
            RecipeRequestRule::RECIPE_INGREDIENT_ID->messages(),
            RecipeRequestRule::INGREDIENT_ID->messages(),
            RecipeRequestRule::INGREDIENT_QUANTITY->messages(),
        );
    }
}
