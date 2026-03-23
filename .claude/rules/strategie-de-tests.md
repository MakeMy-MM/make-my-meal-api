---
description: Enforce the following testing strategies, class structures, and naming conventions strictly during all test generation, debugging, and quality assurance tasks.
---

# Stratégie de Tests

## Types de tests

### Tests Unitaires

- Placés dans `tests/Unit/`.
- Héritent de `TestUnitCase` qui fournit `createConfiguredModelMock()`.
- Testent une unité de code isolée (Service, DTO, etc.) sans toucher à la base de données.
- Les dépendances (Repositories, autres Services) sont mockées.

#### Quand écrire un test unitaire

- Logique métier dans un Service (calculs, conditions, transformations).
- Comportement d'un DTO (toArray, filtrage des nulls, getters).
- Validation des Enums de règles.

#### Conventions de structure

- Pas de `setUp()` ni de propriétés de classe : chaque dépendance est créée par une **méthode privée** dédiée.
- **Tout est mocké** sauf le service testé et les classes finales.
- Les mocks retournent un type intersection (`ClassName&MockObject`).
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

## Règles générales

- Chaque fichier de test ne teste qu'un seul sujet (un endpoint, un service, un DTO).
- Nommage des tests unitaires : `testNomFonctionCasCeQueCaDoitFaire`.
  - Succès : `testCreateReturnsIngredient`, `testGetByUserReturnsCollection`.
  - Exception : `testCreateThrowsInternalServerErrorHttpException`.
- Nommage des tests d'intégration : `test{Method}{Action}With{Condition}Returns{HttpCode}`.
  - La partie `With{Condition}` est optionnelle (omise pour le cas nominal).
  - Succès : `testPostRegisterReturnsCreated`, `testGetIndexReturnsSuccess`.
  - Erreur : `testPostRegisterWithEmptyBodyReturnsUnprocessableEntity`, `testGetShowWithInvalidIdReturnsNotFound`, `testPostLogoutWithoutAccessTokenReturnsUnauthorized`, `testPostLogoutWithAlreadyUsedTokenReturnsUnauthorized`.
- Ordre des tests par route : **Success → Unauthorized → Without body → reste** (cas spécifiques).
- Les tests d'intégration d'un même controller sont regroupés dans un seul fichier.
- Les méthodes helper privées sont placées **après** les tests.
- La structure des dossiers de tests reflète la structure des domaines métier.
