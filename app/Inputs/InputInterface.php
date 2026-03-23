<?php

namespace App\Inputs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

interface InputInterface
{
    /** @param array<string, Model> $models */
    public static function fromRequest(FormRequest $data, array $models): static;
}
