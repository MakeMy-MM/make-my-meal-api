<?php

namespace App\Domain\Recipe\Models;

use App\Domain\Recipe\Enums\RecipeType;
use App\Domain\User\Models\User;
use App\Models\OwnerInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model implements OwnerInterface
{
    use HasUuids;

    protected $fillable = ['type', 'name', 'image', 'user_id'];

    protected $casts = [
        'type' => RecipeType::class,
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<RecipeIngredient, $this> */
    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /** @return HasMany<RecipeStep, $this> */
    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class);
    }

    public function getOwner(): ?User
    {
        return $this->user;
    }
}
