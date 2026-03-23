<?php

namespace App\DTOs;

interface DTOInterface
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
