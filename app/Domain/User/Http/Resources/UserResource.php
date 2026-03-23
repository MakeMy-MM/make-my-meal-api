<?php

namespace App\Domain\User\Http\Resources;

use App\Http\Resources\BasicResource;
use Illuminate\Http\Request;

/** @mixin \App\Domain\User\Models\User */
class UserResource extends BasicResource
{
    public static $wrap = 'user';

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'email_verified' => $this->email_verified_at !== null,
        ];
    }
}
