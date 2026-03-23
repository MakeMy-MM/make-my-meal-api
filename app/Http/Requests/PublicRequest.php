<?php

namespace App\Http\Requests;

abstract class PublicRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
