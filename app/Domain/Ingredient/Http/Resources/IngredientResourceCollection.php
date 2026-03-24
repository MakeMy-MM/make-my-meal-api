<?php

namespace App\Domain\Ingredient\Http\Resources;

use App\Http\Resources\BasicResourceCollection;

class IngredientResourceCollection extends BasicResourceCollection
{
    public static $wrap = 'ingredients';

    public $collects = IngredientResource::class;
}
