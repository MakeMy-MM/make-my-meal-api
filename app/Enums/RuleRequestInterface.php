<?php

namespace App\Enums;

interface RuleRequestInterface
{
    /** @return array<int, mixed> */
    public function rules(): array;

    /** @return array<string, string> */
    public function messages(string $prefix = ''): array;
}
