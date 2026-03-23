<?php

namespace App\DTOs;

abstract class BaseFieldDTO implements DTOInterface
{
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return array_filter($this->getProperties(), fn($value) => $value !== null);
    }

    /** @return array<string, mixed> */
    abstract protected function getProperties(): array;
}
