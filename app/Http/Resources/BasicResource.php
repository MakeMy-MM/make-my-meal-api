<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BasicResource extends JsonResource
{
    public static $wrap = null;

    public function nowrap(): static
    {
        static::$wrap = null;

        return $this;
    }
}
