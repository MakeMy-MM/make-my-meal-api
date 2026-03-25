<?php

namespace App\Domain\Recipe\Enums;

use App\Enums\RuleRequestInterface;

enum RecipeIngredientRequestRule: string implements RuleRequestInterface
{
    case INGREDIENT_ID = 'id';
    case QUANTITY = 'quantity';

    public function rules(): array
    {
        return match ($this) {
            self::INGREDIENT_ID => [
                'uuid',
                'exists:ingredients,id',
            ],
            self::QUANTITY => [
                'numeric',
                'min:0.01',
            ],
        };
    }

    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::INGREDIENT_ID => [
                $prefix . self::INGREDIENT_ID->value . '.required' => 'recipe_ingredient.' . self::INGREDIENT_ID->value . '.required',
                $prefix . self::INGREDIENT_ID->value . '.uuid' => 'recipe_ingredient.' . self::INGREDIENT_ID->value . '.uuid',
                $prefix . self::INGREDIENT_ID->value . '.exists' => 'recipe_ingredient.' . self::INGREDIENT_ID->value . '.exists',
            ],
            self::QUANTITY => [
                $prefix . self::QUANTITY->value . '.required' => 'recipe_ingredient.' . self::QUANTITY->value . '.required',
                $prefix . self::QUANTITY->value . '.numeric' => 'recipe_ingredient.' . self::QUANTITY->value . '.numeric',
                $prefix . self::QUANTITY->value . '.min' => 'recipe_ingredient.' . self::QUANTITY->value . '.min',
            ],
        };
    }
}
