<?php

namespace App\Domain\Recipe\Http\Resources;

use App\Http\Resources\BasicResourceCollection;

class RecipeResourceCollection extends BasicResourceCollection
{
    public $collects = RecipeResource::class;

    public function __construct(mixed $resource)
    {
        parent::__construct($resource);
        static::$wrap = RecipeResource::$wrapCollection;
    }
}
