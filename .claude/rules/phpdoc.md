---
description: Enforce the following PHPDoc conventions, static typing rules, and annotation structures strictly during all code generation, typing, and documentation tasks.
---

## Principes généraux

- Les PHPDoc servent principalement au **typage statique** (PHPStan niveau 8) et à l'**autocomplétion IDE**.
- Ne pas ajouter de PHPDoc si le type natif PHP suffit déjà. Privilégier les PHPDoc pour les **generics**, les **collections typées**, et les **annotations de classe**.
- **Ne pas re-déclarer les PHPDoc sur les méthodes enfants** quand le parent ou l'interface définit déjà le type de retour. Cela s'applique notamment aux : implémentations de `RuleRequestInterface` (`rules()`, `messages()`), méthodes héritées des Resources (`toArray()`), méthodes héritées des Requests (`rules()`, `messages()`). Ne réécrire un PHPDoc que si le type enfant est **plus précis** que celui du parent (ex: array shape, generic concret).

## Resources : `@mixin`

- Chaque Resource de domaine utilise `@mixin` pour référencer le model sous-jacent.
- Cela permet l'autocomplétion des propriétés du model dans `toArray()`.

```php
/** @mixin \App\Domain\Ingredient\Models\Ingredient */
class IngredientResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // autocomplété grâce à @mixin
        ];
    }
}
```

## Repositories : `@extends` et `@method`

- Les Repositories utilisent `@extends` pour spécifier le type concret du template générique de `ModelRepository`.
- Les méthodes héritées de `ModelRepository` sont re-déclarées via `@method` avec le type concret du model.

```php
/**
 * @extends ModelRepository<Ingredient>
 *
 * @method Collection<int, Ingredient> findByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 * @method Ingredient|null              findFirstByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 */
class IngredientRepository extends ModelRepository
```

## Relations Eloquent : generics

- Les relations sont typées avec les generics PHPDoc `<ModelCible, $this>`.

```php
/** @return HasMany<Recipe, $this> */
public function recipes(): HasMany
{
    return $this->hasMany(Recipe::class);
}

/** @return BelongsTo<User, $this> */
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
```

## Factories : `@use` sur le trait

- Quand un model utilise `HasFactory`, annoter le trait avec le type concret de la factory.

```php
/** @use HasFactory<UserFactory> */
use HasFactory;
```

## Inputs : `$models` typé en array shape

- Le paramètre `$models` de `fromRequest()` est typé avec un **array shape** PHPDoc pour forcer les clés et types attendus.
- Chaque Input déclare les models spécifiques dont il a besoin.

```php
/**
 * @param CreateIngredientRequest $data
 * @param array{user: User} $models
 */
public static function fromRequest(FormRequest $data, array $models): static
```

## Retours de méthode

- Typer les retours de `toArray()` avec `@return array<string, mixed>`.
- Typer les retours de `rules()` avec `@return array<string, ValidationRule|array<mixed>|string>`.
- Typer les retours de `messages()` avec `@return array<string, string>`.
- Typer les paramètres de collection avec `@param array<int, mixed>`.
