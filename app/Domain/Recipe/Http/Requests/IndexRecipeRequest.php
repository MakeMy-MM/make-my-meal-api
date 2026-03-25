<?php

namespace App\Domain\Recipe\Http\Requests;

use App\Http\Requests\RoleRequest;

class IndexRecipeRequest extends RoleRequest
{
    public function authorize(): bool
    {
        return $this->isSelf();
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }
}
