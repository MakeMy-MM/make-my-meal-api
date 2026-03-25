<?php

namespace App\Domain\Recipe\Models;

use App\Domain\User\Models\User;
use App\Models\OwnerInterface;
use Database\Factories\RecipeStepFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecipeStep extends Model implements OwnerInterface
{
    /** @use HasFactory<RecipeStepFactory> */
    use HasFactory, HasUuids;

    protected $fillable = ['position', 'description', 'recipe_id'];

    public $timestamps = false;

    protected static function newFactory(): RecipeStepFactory
    {
        return RecipeStepFactory::new();
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
