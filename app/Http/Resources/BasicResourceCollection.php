<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class BasicResourceCollection extends ResourceCollection
{
    public static $wrap = null;

    public function nowrap(): static
    {
        static::$wrap = null;

        return $this;
    }

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        Assert::isInstanceOf($this->collection, Collection::class);

        return [
            'count' => $this->collection->count(),
            'items' => $this->collection,
        ];
    }
}
