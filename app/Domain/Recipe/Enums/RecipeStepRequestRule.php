<?php

namespace App\Domain\Recipe\Enums;

use App\Enums\RuleRequestInterface;

enum RecipeStepRequestRule: string implements RuleRequestInterface
{
    case DESCRIPTION = 'description';

    public function rules(): array
    {
        return match ($this) {
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
            self::DESCRIPTION => [
                $prefix . self::DESCRIPTION->value . '.required' => 'recipe_step.' . self::DESCRIPTION->value . '.required',
                $prefix . self::DESCRIPTION->value . '.string' => 'recipe_step.' . self::DESCRIPTION->value . '.string',
                $prefix . self::DESCRIPTION->value . '.min' => 'recipe_step.' . self::DESCRIPTION->value . '.min',
                $prefix . self::DESCRIPTION->value . '.max' => 'recipe_step.' . self::DESCRIPTION->value . '.max',
            ],
        };
    }
}
