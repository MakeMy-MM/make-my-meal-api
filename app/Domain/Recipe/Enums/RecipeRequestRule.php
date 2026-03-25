<?php

namespace App\Domain\Recipe\Enums;

use App\Enums\RuleRequestInterface;
use Illuminate\Validation\Rules\Enum;

enum RecipeRequestRule: string implements RuleRequestInterface
{
    public const string STEP = 'steps.*.';
    public const string INGREDIENT = 'ingredients.*.';

    case NAME = 'name';
    case TYPE = 'type';
    case STEPS = 'steps';
    case STEP_DESCRIPTION = self::STEP . 'description';
    case INGREDIENTS = 'ingredients';
    case INGREDIENT_ID = self::INGREDIENT . 'id';
    case INGREDIENT_QUANTITY = self::INGREDIENT . 'quantity';

    public function rules(): array
    {
        return match ($this) {
            self::NAME => [
                'string',
                'min:3',
                'max:127',
            ],
            self::TYPE => [
                new Enum(RecipeType::class),
            ],
            self::STEPS => [
                'array',
                'min:1',
            ],
            self::STEP_DESCRIPTION => RecipeStepRequestRule::DESCRIPTION->rules(),
            self::INGREDIENTS => [
                'array',
                'min:1',
            ],
            self::INGREDIENT_ID => RecipeIngredientRequestRule::INGREDIENT_ID->rules(),
            self::INGREDIENT_QUANTITY => RecipeIngredientRequestRule::QUANTITY->rules(),
        };
    }

    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::NAME => [
                $prefix . self::NAME->value . '.required' => 'recipe.' . self::NAME->value . '.required',
                $prefix . self::NAME->value . '.string' => 'recipe.' . self::NAME->value . '.string',
                $prefix . self::NAME->value . '.min' => 'recipe.' . self::NAME->value . '.min',
                $prefix . self::NAME->value . '.max' => 'recipe.' . self::NAME->value . '.max',
            ],
            self::TYPE => [
                $prefix . self::TYPE->value . '.required' => 'recipe.' . self::TYPE->value . '.required',
                $prefix . self::TYPE->value => 'recipe.' . self::TYPE->value . '.enum',
            ],
            self::STEPS => [
                $prefix . self::STEPS->value . '.required' => 'recipe.' . self::STEPS->value . '.required',
                $prefix . self::STEPS->value . '.array' => 'recipe.' . self::STEPS->value . '.array',
                $prefix . self::STEPS->value . '.min' => 'recipe.' . self::STEPS->value . '.min',
            ],
            self::STEP_DESCRIPTION => RecipeStepRequestRule::DESCRIPTION->messages($prefix . self::STEP),
            self::INGREDIENTS => [
                $prefix . self::INGREDIENTS->value . '.required' => 'recipe.' . self::INGREDIENTS->value . '.required',
                $prefix . self::INGREDIENTS->value . '.array' => 'recipe.' . self::INGREDIENTS->value . '.array',
                $prefix . self::INGREDIENTS->value . '.min' => 'recipe.' . self::INGREDIENTS->value . '.min',
            ],
            self::INGREDIENT_ID => RecipeIngredientRequestRule::INGREDIENT_ID->messages($prefix . self::INGREDIENT),
            self::INGREDIENT_QUANTITY => RecipeIngredientRequestRule::QUANTITY->messages($prefix . self::INGREDIENT),
        };
    }
}
