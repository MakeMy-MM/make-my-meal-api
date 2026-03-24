---
description: Enforce the following architectural conventions, class responsibilities, and data flow constraints strictly during all component generation, refactoring, and structural planning tasks.
---

## Flow d'une route

`Request → Controller → Input (fromRequest) → Service (via interface) → Repository → Model`

## Configuration des Models

- Utiliser les **propriétés** (`$fillable`, `$hidden`, `$casts`, `$timestamps`, `$table`) et non les méthodes ou les attributs PHP.
- Tous les models utilisent des UUID via le trait natif `HasUuids` de Laravel.
- Les relations doivent être typées avec les génériques PHPDoc : `@return RelationType<ModelCible, $this>`.
- Les models sont placés dans `app/Domain/{Domaine}/Models/`.
- Quand un model n'est pas dans `App\Models\`, overrider `newFactory()` pour la résolution des factories.
- Les models possédés par un utilisateur implémentent `OwnerInterface` (élément global dans `app/Models/`) avec la méthode `getOwner(): User`.

## Inputs

- Les Inputs font le pont entre la Request et le DTO.
- Chaque Input implémente `InputInterface` (élément global dans `app/Inputs/`) qui définit `fromRequest(Request $data, array $models): self`.
- Un Input étend le DTO correspondant (ex: `CreateIngredientInput extends CreateIngredientDTO`).
- La méthode `fromRequest()` extrait les données validées de la Request et les models des route parameters.
- Les Inputs sont placés dans `app/Domain/{Domaine}/Inputs/`.

## DTOs

- Les données validées passent par un Input/DTO avant d'être envoyées au service.
- Hiérarchie des DTOs (éléments globaux dans `app/DTOs/`) :
  - `DTOInterface` : contrat avec `toArray(): array`.
  - `BaseFieldDTO` : classe abstraite implémentant `DTOInterface`, avec `toArray()` qui filtre les valeurs nulles via `getProperties(): array`.
  - `UpdateDTOInterface` : contrat avec `getModel(): Model`.
- Le `Fields{Entité}DTO` est rattaché au domaine du model. Les DTOs d'action d'autres domaines étendent ce DTO de model.
- Par domaine de model :
  - `Fields{Entité}DTO` : étend `BaseFieldDTO`, tous les champs en nullable, implémente `getProperties()`.
  - `Create{Entité}DTO` : étend `Fields{Entité}DTO`, champs requis.
  - `Update{Entité}DTO` : étend `Fields{Entité}DTO`, implémente `UpdateDTOInterface`, model + champs optionnels.

## Services

- **Un service par action** (ex: `RegisterService`, `LoginService`, `CreateRecipeService`), pas un service global par domaine.
- Nommage : `{Action}{Entité}Service` (ex: `CreateIngredientService`, `UpdateIngredientService`, `DeleteIngredientService`).
- Chaque service doit implémenter une interface (`{Action}{Entité}ServiceInterface`).
- Le binding se fait via la propriété `$bindings` dans le ServiceProvider du domaine.
- Le controller injecte l'interface, pas l'implémentation.
- Les services retournent des **Models ou Collections**, jamais des Resources.
- Les opérations d'écriture (create, update, delete) sont encapsulées dans des transactions explicites (`beginTransaction / commit / rollBack`).

## Repositories

- Chaque domaine a son propre Repository dans `{Domaine}/Repositories/`.
- Les Repositories étendent `ModelRepository` (élément global dans `app/Repositories/`).
- `ModelRepository` fournit `findByFields()` et `findFirstByFields()`.
- Les Repositories spécialisés ajoutent `create()`, `update()`, `delete()`.
- Les erreurs dans les Repositories sont wrappées dans `InternalServerErrorHttpException`.
- Chaque Repository enfant re-déclare les méthodes héritées via `@method` avec le type concret.

## Providers

- Chaque domaine a son propre ServiceProvider dans `{Domaine}/Providers/`.
- Utiliser la propriété `$bindings` pour les bindings interface → implémentation.
- Les ServiceProviders sont enregistrés dans `bootstrap/providers.php`.

## Requests

- Hiérarchie des Requests (éléments globaux dans `app/Http/Requests/`) :
  - `BaseRequest` : classe abstraite, gère le format de validation standardisé, fournit `requiredRules()` / `optionnalRules()`.
  - `PublicRequest` : `authorize()` retourne `true`. Routes publiques.
  - `RoleRequest` : vérifie l'ownership via `OwnerInterface`. Routes authentifiées.
- `BaseRequest` lève une `LogicException` si `messages()` n'est pas overridée.

## Validation via Enums

- Règles centralisées dans des enums rattachés au domaine du **model** (ex: `UserRequestRule` dans `User/Enums/`).
- Chaque enum implémente `RuleRequestInterface` qui définit `rules(): array` et `messages(string $prefix = ''): array`.
- Les Requests composent les règles via `requiredRules()` (create) ou `optionnalRules()` (update).
- Clés des messages : `{prefix}{field}.{rule}`. Valeurs : `{domaine}.{field}.{rule}` (i18n).
- Le `$prefix` est appliqué sur les **clés** (validation). Il sert quand les champs d'un domaine sont imbriqués dans le payload d'un autre (ex: une Request qui valide `ingredient.name` appelle `IngredientRequestRule::NAME->messages('ingredient.')`).

## Resources

- Les Resources étendent `BasicResource`, les ResourceCollections étendent `BasicResourceCollection`.
- Chaque Resource override `$wrap` avec le nom de la ressource.
- Les collections utilisent la structure `{ count, items }`.
- Le controller construit les Resources via la chaîne fluide : `(new Resource($model))->additional([...])->response()->setStatusCode(...)`.

## Authentification (Passport + Refresh Token custom)

### Système dual-token

- **Access token** : JWT Passport via personal access grant, TTL court (configurable via `config('auth.tokens.access.expiration')`).
- **Refresh token** : token opaque custom, hashé en DB (algo configurable via `config('auth.tokens.refresh.hash_algo')`), TTL long (configurable via `config('auth.tokens.refresh.expiration')`).
- Les deux tokens sont toujours créés ensemble via `TokenService::create()` qui retourne un `TokenDTO`.
- La réponse JSON utilise `TokenResource` wrappée dans `tokens` via `additional()` sur la `UserResource`.
- L'endpoint `/refresh` retourne uniquement les tokens (pas de user).

### Configuration

- Le guard API est configuré dans `config/auth.php` avec le driver `passport`.
- Les TTL et l'algo de hash sont dans `config('auth.tokens')`.
- Le TTL Passport est appliqué dans `AuthServiceProvider::boot()`.
- Les migrations Passport utilisent `foreignUuid` pour `user_id` (UUID).

### Model User

- Implémente `OAuthenticatable` et utilise le trait `HasApiTokens` de Passport.
- Ne contient **pas** de logique de création de token — c'est le `TokenService` qui appelle `$user->createToken('access')`.

### Model RefreshToken

- Placé dans `Auth/Models/` (concern auth, pas user).
- Pas de `$timestamps = false` (a besoin de `created_at` pour le suivi).
- `isExpired()` vérifie l'expiration.
- Supprimé physiquement (pas de soft delete ni révocation).
