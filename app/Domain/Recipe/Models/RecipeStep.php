<?php

namespace App\Domain\Recipe\Models;

use App\Domain\User\Models\User;
use App\Models\OwnerInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeStep extends Model implements OwnerInterface
{
    use HasUuids;

    protected $fillable = ['position', 'description', 'recipe_id'];

    public $timestamps = false;

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
