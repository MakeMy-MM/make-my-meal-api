<?php

namespace App\Domain\Auth\Enums;

use App\Enums\RuleRequestInterface;

enum TokenRequestRule: string implements RuleRequestInterface
{
    case REFRESH_TOKEN = 'refresh_token';

    /** @return array<int, mixed> */
    public function rules(): array
    {
        return match ($this) {
            self::REFRESH_TOKEN => [
                'string',
            ],
        };
    }

    /** @return array<string, string> */
    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::REFRESH_TOKEN => [
                $prefix . self::REFRESH_TOKEN->value . '.required' => 'auth.' . self::REFRESH_TOKEN->value . '.required',
                $prefix . self::REFRESH_TOKEN->value . '.string' => 'auth.' . self::REFRESH_TOKEN->value . '.string',
            ],
        };
    }
}
