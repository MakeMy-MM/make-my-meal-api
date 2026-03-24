<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Webmozart\Assert\Assert;

class BasicResourceCollection extends ResourceCollection
{
    public static $wrap = null;

    /** @var class-string<mixed> */
    public $collects = BasicResource::class;

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
