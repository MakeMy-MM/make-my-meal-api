<?php

namespace App\Domain\User\Enums;

use App\Enums\RuleRequestInterface;
use Illuminate\Validation\Rule;

enum UserRequestRule: string implements RuleRequestInterface
{
    case EMAIL = 'email';
    case PASSWORD = 'password';

    public function rules(): array
    {
        return match ($this) {
            self::EMAIL => [
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            self::PASSWORD => [
                'string',
                'min:8',
                'max:255',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
        };
    }

    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::EMAIL => [
                $prefix . self::EMAIL->value . '.required' => 'user.' . self::EMAIL->value . '.required',
                $prefix . self::EMAIL->value . '.string' => 'user.' . self::EMAIL->value . '.string',
                $prefix . self::EMAIL->value . '.max' => 'user.' . self::EMAIL->value . '.max',
                $prefix . self::EMAIL->value . '.email' => 'user.' . self::EMAIL->value . '.email',
                $prefix . self::EMAIL->value . '.unique' => 'user.' . self::EMAIL->value . '.unique',
            ],
            self::PASSWORD => [
                $prefix . self::PASSWORD->value . '.required' => 'user.' . self::PASSWORD->value . '.required',
                $prefix . self::PASSWORD->value . '.string' => 'user.' . self::PASSWORD->value . '.string',
                $prefix . self::PASSWORD->value . '.min' => 'user.' . self::PASSWORD->value . '.regex',
                $prefix . self::PASSWORD->value . '.max' => 'user.' . self::PASSWORD->value . '.max',
                $prefix . self::PASSWORD->value . '.regex' => 'user.' . self::PASSWORD->value . '.regex',
            ],
        };
    }
}
