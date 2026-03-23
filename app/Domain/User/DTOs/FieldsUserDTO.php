<?php

namespace App\Domain\User\DTOs;

use App\DTOs\BaseFieldDTO;
use Carbon\CarbonImmutable;

class FieldsUserDTO extends BaseFieldDTO
{
    public function __construct(
        protected readonly ?string $email = null,
        protected readonly ?CarbonImmutable $email_verified_at = null,
        protected readonly ?string $password = null,
    ) {}

    /** @return array<string, mixed> */
    protected function getProperties(): array
    {
        return [
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'password' => $this->password,
        ];
    }
}
