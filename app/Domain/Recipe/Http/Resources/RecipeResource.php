<?php

namespace App\Domain\Recipe\Http\Resources;

use App\Http\Resources\BasicResource;
use Illuminate\Http\Request;

/** @mixin \App\Domain\Recipe\Models\Recipe */
class RecipeResource extends BasicResource
{
    public static $wrap = 'recipe';

    public static string $wrapCollection = 'recipes';

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            RecipeStepResource::$wrapCollection => RecipeStepResource::collection($this->steps),
            RecipeIngredientResource::$wrapCollection => RecipeIngredientResource::collection($this->recipeIngredients),
        ];
    }
}
