<?php

namespace App\Domain\Ingredient\Enums;

use App\Enums\RuleRequestInterface;
use Illuminate\Validation\Rules\Enum;

enum IngredientRequestRule: string implements RuleRequestInterface
{
    case ID = 'id';
    case NAME = 'name';
    case UNIT = 'unit';

    public function rules(): array
    {
        return match ($this) {
            self::ID => [
                'uuid',
            ],
            self::NAME => [
                'string',
                'min:3',
                'max:63',
            ],
            self::UNIT => [
                new Enum(MeasurementUnit::class),
            ],
        };
    }

    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::ID => [
                $prefix . self::ID->value . '.required' => 'ingredient.' . self::ID->value . '.required',
                $prefix . self::ID->value . '.required_without' => 'ingredient.' . self::ID->value . '.required',
                $prefix . self::ID->value . '.uuid' => 'ingredient.' . self::ID->value . '.uuid',
                $prefix . self::ID->value . '.exists' => 'ingredient.' . self::ID->value . '.exists',
            ],
            self::NAME => [
                $prefix . self::NAME->value . '.required' => 'ingredient.' . self::NAME->value . '.required',
                $prefix . self::NAME->value . '.string' => 'ingredient.' . self::NAME->value . '.string',
                $prefix . self::NAME->value . '.min' => 'ingredient.' . self::NAME->value . '.min',
                $prefix . self::NAME->value . '.max' => 'ingredient.' . self::NAME->value . '.max',
            ],
            self::UNIT => [
                $prefix . self::UNIT->value . '.required' => 'ingredient.' . self::UNIT->value . '.required',
                $prefix . self::UNIT->value => 'ingredient.' . self::UNIT->value . '.enum',
            ],
        };
    }
}
