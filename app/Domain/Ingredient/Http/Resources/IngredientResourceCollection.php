<?php

namespace App\Domain\Ingredient\Http\Resources;

use App\Http\Resources\BasicResourceCollection;

class IngredientResourceCollection extends BasicResourceCollection
{
    public $collects = IngredientResource::class;

    public function __construct(mixed $resource)
    {
        parent::__construct($resource);
        static::$wrap = IngredientResource::$wrapCollection;
    }
}
