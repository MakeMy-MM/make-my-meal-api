<?php

namespace App\Domain\Auth\DTOs;

use App\Domain\User\DTOs\FieldsUserDTO;
use Webmozart\Assert\Assert;

class RegisterDTO extends FieldsUserDTO
{
    public function __construct(
        string $email,
        string $password,
    ) {
        parent::__construct(
            email: $email,
            password: $password,
        );
    }

    public function getEmail(): string
    {
        Assert::notNull($this->email);

        return $this->email;
    }

    public function getPassword(): string
    {
        Assert::notNull($this->password);

        return $this->password;
    }
}
