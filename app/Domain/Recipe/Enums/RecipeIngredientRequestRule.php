<?php

namespace App\Domain\Recipe\Enums;

use App\Domain\Ingredient\Enums\IngredientRequestRule;
use App\Enums\RuleRequestInterface;

enum RecipeIngredientRequestRule: string implements RuleRequestInterface
{
    case ID = 'id';
    case QUANTITY = 'quantity';
    case INGREDIENT_ID = 'ingredient_id';

    public function rules(): array
    {
        return match ($this) {
            self::ID => [
                'uuid',
                'exists:recipe_ingredients,id',
            ],
            self::QUANTITY => [
                'numeric',
                'min:0.01',
            ],
            self::INGREDIENT_ID => IngredientRequestRule::ID->rules(),
        };
    }

    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::ID => [
                $prefix . self::ID->value . '.required' => 'recipe_ingredient.' . self::ID->value . '.required',
                $prefix . self::ID->value . '.uuid' => 'recipe_ingredient.' . self::ID->value . '.uuid',
                $prefix . self::ID->value . '.exists' => 'recipe_ingredient.' . self::ID->value . '.exists',
            ],
            self::QUANTITY => [
                $prefix . self::QUANTITY->value . '.required' => 'recipe_ingredient.' . self::QUANTITY->value . '.required',
                $prefix . self::QUANTITY->value . '.required_without' => 'recipe_ingredient.' . self::QUANTITY->value . '.required',
                $prefix . self::QUANTITY->value . '.numeric' => 'recipe_ingredient.' . self::QUANTITY->value . '.numeric',
                $prefix . self::QUANTITY->value . '.min' => 'recipe_ingredient.' . self::QUANTITY->value . '.min',
            ],
            self::INGREDIENT_ID => [
                $prefix . self::INGREDIENT_ID->value . '.required' => 'recipe_ingredient.' . self::INGREDIENT_ID->value . '.required',
                $prefix . self::INGREDIENT_ID->value . '.required_without' => 'recipe_ingredient.' . self::INGREDIENT_ID->value . '.required',
                $prefix . self::INGREDIENT_ID->value . '.uuid' => 'recipe_ingredient.' . self::INGREDIENT_ID->value . '.uuid',
                $prefix . self::INGREDIENT_ID->value . '.exists' => 'recipe_ingredient.' . self::INGREDIENT_ID->value . '.exists',
            ],
        };
    }
}
