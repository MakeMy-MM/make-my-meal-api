<?php

namespace App\Domain\Recipe\Http\Resources;

use App\Http\Resources\BasicResource;
use Illuminate\Http\Request;
use Webmozart\Assert\Assert;

/** @mixin \App\Domain\Recipe\Models\RecipeIngredient */
class RecipeIngredientResource extends BasicResource
{
    public static $wrap = 'ingredient';

    public static string $wrapCollection = 'ingredients';

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        Assert::notNull($this->ingredient);

        return [
            'id' => $this->id,
            'ingredient_id' => $this->ingredient->id,
            'position' => $this->position,
            'quantity' => $this->quantity,
            'name' => $this->ingredient->name,
            'unit' => $this->ingredient->measurement_unit,
        ];
    }
}
