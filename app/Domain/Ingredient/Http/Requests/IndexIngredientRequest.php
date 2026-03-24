<?php

namespace App\Domain\Ingredient\Http\Requests;

use App\Http\Requests\RoleRequest;

class IndexIngredientRequest extends RoleRequest
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
