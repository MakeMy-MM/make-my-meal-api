<?php

namespace App\Domain\User\Models;

use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\Recipe\Models\Recipe;
use App\Models\OwnerInterface;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements OAuthenticatable, OwnerInterface
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $fillable = ['email', 'password'];

    protected $hidden = ['password'];

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /** @return HasMany<Ingredient, $this> */
    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    /** @return HasMany<Recipe, $this> */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function getOwner(): ?User
    {
        return $this;
    }
}
