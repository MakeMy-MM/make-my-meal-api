<?php

namespace App\Domain\Auth\Http\Requests;

use App\Domain\Auth\Enums\TokenRequestRule;
use App\Http\Requests\PublicRequest;

class LogoutRequest extends PublicRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            TokenRequestRule::REFRESH_TOKEN->value => $this->requiredRules(TokenRequestRule::REFRESH_TOKEN->rules()),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return array_merge(
            TokenRequestRule::REFRESH_TOKEN->messages(),
        );
    }
}
