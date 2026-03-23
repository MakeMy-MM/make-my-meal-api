---
description: Enforce the following database design constraints, migration conventions, and Eloquent model configurations strictly during all schema generation, refactoring, and database interaction tasks.
---

## Clés primaires

- Tous les models utilisent des **UUID** comme clé primaire via `$table->uuid('id')->primary()` et le trait `HasUuids`.
- Ne pas utiliser `$table->id()` (auto-increment).

## Clés étrangères

- Utiliser `foreignUuid()` pour les relations entre models.
- Toujours spécifier la **stratégie de suppression** sur chaque clé étrangère :

### Stratégies de suppression

| Stratégie | Quand l'utiliser | Exemple |
|-----------|-----------------|---------|
| `cascadeOnDelete()` | La ressource enfant n'a pas de sens sans le parent | Recipe → User, RecipeStep → Recipe |
| `nullOnDelete()` | La ressource enfant peut exister indépendamment | Ingredient → User (ingrédient partagé) |
| `restrictOnDelete()` | Empêcher la suppression du parent tant que des enfants existent | — |

```php
// L'ingrédient survit à la suppression de l'utilisateur
$table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();

// La recette est supprimée avec l'utilisateur
$table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
```

## Soft Deletes

- Utiliser `softDeletes()` sur les tables dont les données peuvent être restaurées ou sont référencées par d'autres ressources (ex: `ingredients`).
- Ne pas utiliser `softDeletes()` sur les tables de jointure ou les tables enfants qui cascadent déjà.

## Timestamps

- Par défaut, `$timestamps = true` (Laravel default).
- Mettre `public $timestamps = false` sur les models de jointure ou les sous-ressources qui n'ont pas besoin de `created_at` / `updated_at` (ex: `RecipeIngredient`, `RecipeStep`).

## Types de colonnes

- **Quantités décimales** : utiliser `decimal($column, $total, $places)` en migration et `'decimal:N'` dans `$casts` du model.

```php
// Migration
$table->decimal('quantity', 10, 2);

// Model
protected $casts = [
    'quantity' => 'decimal:2',
];
```

- **Enums** : stocker en `string` dans la migration, caster via l'enum dans le model.

```php
// Migration
$table->string('type');

// Model
protected $casts = [
    'type' => RecipeType::class,
];
```

## Nommage des migrations

- Tables Laravel core : préfixe `0001_01_01_` (ex: `0001_01_01_000000_create_users_table.php`).
- Tables domaine : préfixe date du jour (ex: `2026_03_21_100000_create_ingredients_table.php`).
- Incrémenter le timestamp pour respecter l'ordre de dépendance entre les tables.
- Format du nom : `create_{table}_table` pour la création, `add_{column}_to_{table}_table` pour l'ajout de colonnes.

## Méthode down()

- Toujours implémenter `down()` avec `Schema::dropIfExists()` pour les créations de table.
- Pour les modifications de colonnes, inverser l'opération dans `down()`.
