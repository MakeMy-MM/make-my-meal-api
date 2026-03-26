<?php

namespace App\Domain\Recipe\Enums;

use App\Enums\RuleRequestInterface;

enum RecipeStepRequestRule: string implements RuleRequestInterface
{
    case ID = 'id';
    case DESCRIPTION = 'description';

    public function rules(): array
    {
        return match ($this) {
            self::ID => [
                'uuid',
                'exists:recipe_steps,id',
            ],
            self::DESCRIPTION => [
                'string',
                'min:3',
                'max:1023',
            ],
        };
    }

    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::ID => [
                $prefix . self::ID->value . '.required' => 'recipe_step.' . self::ID->value . '.required',
                $prefix . self::ID->value . '.uuid' => 'recipe_step.' . self::ID->value . '.uuid',
                $prefix . self::ID->value . '.exists' => 'recipe_step.' . self::ID->value . '.exists',
            ],
            self::DESCRIPTION => [
                $prefix . self::DESCRIPTION->value . '.required' => 'recipe_step.' . self::DESCRIPTION->value . '.required',
                $prefix . self::DESCRIPTION->value . '.required_without' => 'recipe_step.' . self::DESCRIPTION->value . '.required',
                $prefix . self::DESCRIPTION->value . '.string' => 'recipe_step.' . self::DESCRIPTION->value . '.string',
                $prefix . self::DESCRIPTION->value . '.min' => 'recipe_step.' . self::DESCRIPTION->value . '.min',
                $prefix . self::DESCRIPTION->value . '.max' => 'recipe_step.' . self::DESCRIPTION->value . '.max',
            ],
        };
    }
}
