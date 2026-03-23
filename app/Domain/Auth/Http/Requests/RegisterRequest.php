<?php

namespace App\Domain\Auth\Http\Requests;

use App\Domain\User\Enums\UserRequestRule;
use App\Http\Requests\PublicRequest;

class RegisterRequest extends PublicRequest
{
    public function rules(): array
    {
        return [
            UserRequestRule::EMAIL->value => $this->requiredRules(UserRequestRule::EMAIL->rules()),
            UserRequestRule::PASSWORD->value => $this->requiredRules(UserRequestRule::PASSWORD->rules()),
        ];
    }

    public function messages(): array
    {
        return array_merge(
            UserRequestRule::EMAIL->messages(),
            UserRequestRule::PASSWORD->messages(),
        );
    }
}
