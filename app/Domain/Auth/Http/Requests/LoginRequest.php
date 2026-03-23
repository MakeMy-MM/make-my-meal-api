<?php

namespace App\Domain\Auth\Http\Requests;

use App\Domain\User\Enums\UserRequestRule;
use App\Http\Exceptions\UnauthorizedHttpException;
use App\Http\Requests\PublicRequest;
use Illuminate\Contracts\Validation\Validator;

class LoginRequest extends PublicRequest
{
    public function rules(): array
    {
        return [
            UserRequestRule::EMAIL->value => $this->requiredRules(['string', 'email']),
            UserRequestRule::PASSWORD->value => $this->requiredRules(['string']),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new UnauthorizedHttpException();
    }

    public function messages(): array
    {
        return array_merge(
            UserRequestRule::EMAIL->messages(),
            UserRequestRule::PASSWORD->messages(),
        );
    }
}
