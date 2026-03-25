<?php

namespace App\Domain\Recipe\Http\Resources;

use App\Http\Resources\BasicResource;
use Illuminate\Http\Request;

/** @mixin \App\Domain\Recipe\Models\RecipeStep */
class RecipeStepResource extends BasicResource
{
    public static $wrap = 'step';

    public static string $wrapCollection = 'steps';

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'description' => $this->description,
        ];
    }
}
