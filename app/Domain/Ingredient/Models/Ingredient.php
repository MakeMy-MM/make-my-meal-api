<?php

namespace App\Domain\Ingredient\Models;

use App\Domain\Ingredient\Enums\MeasurementUnit;
use App\Domain\Recipe\Models\RecipeIngredient;
use App\Domain\User\Models\User;
use App\Models\OwnerInterface;
use Database\Factories\IngredientFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model implements OwnerInterface
{
    /** @use HasFactory<IngredientFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'image', 'measurement_unit', 'user_id'];

    protected $casts = [
        'measurement_unit' => MeasurementUnit::class,
    ];

    protected static function newFactory(): IngredientFactory
    {
        return IngredientFactory::new();
    }

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

    public function getOwner(): ?User
    {
        return $this->user;
    }
}
