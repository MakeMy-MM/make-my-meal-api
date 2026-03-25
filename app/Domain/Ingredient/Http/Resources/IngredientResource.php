<?php

namespace App\Domain\Ingredient\Http\Resources;

use App\Http\Resources\BasicResource;
use Illuminate\Http\Request;

/** @mixin \App\Domain\Ingredient\Models\Ingredient */
class IngredientResource extends BasicResource
{
    public static $wrap = 'ingredient';

    public static string $wrapCollection = 'ingredients';

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'unit' => $this->measurement_unit,
        ];
    }
}
