---
description: Enforce the following testing strategies, class structures, and naming conventions strictly during all test generation, debugging, and quality assurance tasks.
---

# Stratégie de Tests

## Types de tests

### Tests Unitaires

- Placés dans `tests/Unit/`.
- Héritent de `TestUnitCase` qui fournit `createConfiguredModelMock()`, `mockTransaction()` et `createFormRequestMock()`.
- Testent une unité de code isolée (Service, Input, etc.) sans toucher à la base de données.
- Les dépendances (Repositories, autres Services) sont mockées.

#### Quand écrire un test unitaire

- Logique métier dans un Service (calculs, conditions, transformations).
- Mapping d'un Input (`fromRequest()` → `toArray()`, getters, filtrage des nulls).
- Validation des Requests (règles et messages).

#### Conventions de structure

- Pas de `setUp()` ni de propriétés de classe : chaque dépendance est créée par une **méthode privée** dédiée.
- **Tout est mocké** sauf le service testé et les classes finales.
- Les mocks retournent un type intersection (`ClassName&MockObject`).
- Les noms des méthodes helper ne doivent **pas** contenir `Mock` (ex: `getUser()`, pas `getUserMock()`).
- Les **collections de models** sont passées en paramètre sous forme d'**array typé** (ex: `array<string, RecipeStep&MockObject>`). La `new Collection($array)` est construite **dans** le helper `getModel()`, pas côté appelant. Cela évite les problèmes de covariance des generics PHPStan avec les mocks.
- Si un model mock est utilisé dans une collection avec `keyBy('id')`, il doit mocker `offsetExists` et `offsetGet` via des callbacks basés sur les attributs, car `data_get()` passe par `ArrayAccess` et non `__get`.
- Les models sont mockés via `createConfiguredModelMock()` avec des valeurs par défaut.
- Les façades (`DB`, etc.) sont mockées via `shouldReceive()` avec `once()`.
- Ordre des méthodes helper privées :
  1. Méthode du service testé.
  2. Dépendances du constructeur du service.
  3. DTOs.
  4. Models.

### Tests d'Intégration

- Placés dans `tests/Feature/`.
- Héritent de `TestFeatureCase` qui préfixe automatiquement `/api/v1`.
- Testent le flux complet : route → middleware → controller → service → repository → DB → réponse.
- Utilisent une vraie base de données (SQLite ou PostgreSQL de test).

#### Quand écrire un test d'intégration

- Chaque endpoint API doit avoir au moins un test pour le cas nominal.
- Tester les cas d'erreur importants (validation, autorisation, 404).
- Tester les flux complexes impliquant plusieurs domaines.

## Base classes

### TestCase

- Classe de base Laravel standard dans `tests/TestCase.php`.

### TestFeatureCase

- Étend `TestCase`.
- Préfixe `/api/v1` sur les méthodes HTTP (`get()`, `post()`, `patch()`, `put()`, `delete()`).
- `post()` utilise `postJson()` en interne.
- Fournit `RefreshDatabase` — ne pas le re-déclarer dans les tests.
- **Obligation d'utiliser `getClient()` ou `getLoggedClient()` avant chaque appel HTTP.** Appeler `$this->get()` directement lève une `LogicException`.
  - `getClient()` : requête non authentifiée.
  - `getLoggedClient(array $attributes)` : requête authentifiée avec un access token Passport. Récupère le user par email ou le crée via factory.
  - Le client se reset après chaque appel HTTP (un `getClient()` / `getLoggedClient()` par requête).

```php
// Non authentifié
$this->getClient()->post('/auth/login', $payload);

// Authentifié (user seedé)
$this->getLoggedClient(['email' => 'user@example.com'])->get('/auth/me');

// Authentifié (user créé à la volée)
$this->getLoggedClient()->get('/auth/me');
```

### TestUnitCase

- Étend `TestCase`.
- Fournit `createConfiguredModelMock(string $originalClassName, array $attributes = [], array $configuration = [])`.
  - `$attributes` : simule les attributs du model (via `__get()`).
  - `$configuration` : configure les méthodes mockées.
- Fournit `mockTransaction()` : mock `DB::transaction()` pour exécuter le callback directement.
- Fournit `createFormRequestMock(string $requestClass, array $validated)` : crée une vraie instance de la Request spécifique avec les données validées. Nécessaire car `FormRequest` a une méthode `method()` qui empêche PHPUnit de la mocker via `createMock()` / `createConfiguredMock()`.
- Fournit `createRequestWithRouteParams(string $requestClass, string $method, string $uri, array $routeParams)` : crée une instance de Request avec des paramètres de route injectés. Utilisé pour tester unitairement les Requests qui accèdent à `$this->route('param')` dans `rules()`. Les paramètres de route non pertinents pour le test sont passés via `$this->createStub(Model::class)`.
- Fournit `containsExistsRule(mixed $rules): bool` : vérifie qu'un tableau de règles contient une instance de `Illuminate\Validation\Rules\Exists`. Utilisé pour tester que les Requests ajoutent bien des règles `Rule::exists()` scopées.

## Règles générales

- Chaque fichier de test ne teste qu'un seul sujet (un endpoint, un service, un input).
- Nommage des tests unitaires : `testNomFonctionCasCeQueCaDoitFaire`.
  - Succès : `testCreateReturnsIngredient`, `testGetByUserReturnsCollection`.
  - Exception : `testCreateThrowsInternalServerErrorHttpException`.
- Nommage des tests d'intégration : `test{Method}{Action}With{Condition}Returns{HttpCode}`.
  - La partie `With{Condition}` est optionnelle (omise pour le cas nominal, qui correspond au Owner).
  - Succès : `testPostCreateAsOwnerReturnsCreated`, `testGetIndexAsOwnerReturnsOk`.
  - Erreur : `testPostCreateAnonymouslyReturnsUnauthorized`, `testPostCreateAsNotOwnerReturnsForbidden`, `testGetShowWithInvalidIdReturnsNotFound`, `testPostCreateWithEmptyBodyReturnsUnprocessableEntity`.
- Conditions d'accès standardisées (conventions de nommage) :
  - `AsOwner` : requête authentifiée avec le propriétaire (`getLoggedClient(['email' => UserSeeder::USER_EMAIL])`) → 2XX.
  - `Anonymously` : requête non authentifiée (`getClient()`) → 401.
  - `AsNotOwner` : requête authentifiée mais pas le propriétaire (`getLoggedClient()` sans email du seedé) → 403.
  - `AsAdmin` : (futur) requête avec un rôle admin.
- Les 3 tests **obligatoires** pour chaque route protégée : `AsOwner` (2XX), `Anonymously` (401), `AsNotOwner` (403).
- Les méthodes de test publiques sont **groupées par fonction/route testée** : tous les cas de `create` ensemble, puis tous les cas de `getByUser`, etc.
- Au sein d'un groupe, l'ordre est : **AsOwner (2XX) → AsNotOwner (403) → Anonymously (401) → Without body (422) → reste** (cas spécifiques).
- Les tests d'intégration d'un même controller sont regroupés dans un seul fichier.
- Les méthodes helper privées sont placées **après** tous les tests.
- La structure des dossiers de tests reflète la structure des domaines métier.
