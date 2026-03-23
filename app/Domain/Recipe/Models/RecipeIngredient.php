<?php

namespace App\Domain\Recipe\Models;

use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\User\Models\User;
use App\Models\OwnerInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeIngredient extends Model implements OwnerInterface
{
    use HasUuids;

    protected $fillable = ['position', 'quantity', 'ingredient_id', 'recipe_id'];

    public $timestamps = false;

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    /** @return BelongsTo<Ingredient, $this> */
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /** @return BelongsTo<Recipe, $this> */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function getOwner(): ?User
    {
        return $this->recipe?->getOwner();
    }
}
