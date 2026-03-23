<?php

namespace App\Utils;

class RoutePatterns
{
    private const string UUID_PATTERN = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    /** @return array<string, string> */
    public static function getPatterns(): array
    {
        return [
            'user' => self::UUID_PATTERN,
            'ingredient' => self::UUID_PATTERN,
            'recipe' => self::UUID_PATTERN,
        ];
    }
}
