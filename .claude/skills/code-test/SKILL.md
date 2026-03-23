---
name: code-test
description: Guide procédural pour créer les tests (unitaires et intégration) d'un composant. A invoquer lors de l'écriture de tests.
user-invocable: true
---

# Skill: Code Test

Guide procédural pour la création de tests. Les templates de composants sont dans le skill `/code` — se baser dessus pour comprendre la structure du code à tester.

## Quoi tester

### Tests obligatoires

| Composant | Type de test | Obligatoire |
|-----------|-------------|-------------|
| Service | Unitaire | Oui — chaque méthode publique |
| DTO | Unitaire | Oui — `toArray()`, `getProperties()`, `getModel()` |
| Enum de validation | Unitaire | Oui — `rules()` et `messages()` |
| Request (rules) | Unitaire | Oui — vérifier les règles de validation retournées |
| Controller (route) | Intégration | Oui — minimum 3 cas par route |
| Repository | Aucun | Non — couvert par les tests d'intégration |

### Couverture exhaustive des branches

**Règle absolue : chaque branche logique doit être testée.**

- S'il y a 4 `if`, écrire au minimum 4 tests (un par branche).
- Chaque `match`, `switch`, `try/catch` doit avoir ses cas couverts.
- Les cas limites (null, vide, valeurs extrêmes) doivent être testés.
- Si un cas semble inatteignable, **ne pas le sauter** : investiguer pourquoi et demander de l'aide à l'utilisateur.

## Tests d'intégration (Controller)

### Seeders comme fixtures

Les tests d'intégration utilisent les **seeders comme fixtures** :

- `TestFeatureCase` a `$seed = true` → les seeders tournent automatiquement avant chaque test.
- Chaque model a son propre seeder dans `database/seeders/` (ex: `UserSeeder`, `IngredientSeeder`).
- Les tests utilisent les données seedées au lieu de `factory()->create()` quand possible.
- Pour récupérer un user seedé : `User::where('email', 'user@example.com')->firstOrFail()`.
- Les factories restent utilisées pour créer des données **spécifiques au test** (ex: un second user pour tester le 403).

### Seeder : convention

Chaque seeder crée des données **déterministes** avec des valeurs connues :

```php
class {Entité}Seeder extends Seeder
{
    public function run(): void
    {
        $this->create('{entité}');
    }

    private function create(string $name): void
    {
        {Entité}::factory()->create([
            // champs déterministes pour les tests
        ]);
    }
}
```

- Enregistrer dans `DatabaseSeeder::run()` via `$this->call([...])`.

### Cas minimum par route

Chaque route **doit** avoir au minimum ces 3 tests, **dans cet ordre** :

| Ordre | Cas | Code HTTP | Description |
|-------|-----|-----------|-------------|
| 1 | Success | 2XX | Requête valide avec le bon utilisateur |
| 2 | Unauthorized | 401 | Requête sans token d'authentification |
| 3 | Forbidden | 403 | Requête avec token mais l'utilisateur n'est pas propriétaire de la ressource |

### Cas supplémentaires selon le contexte (après les 3 cas minimum)

| Ordre | Cas | Code HTTP | Quand l'ajouter |
|-------|-----|-----------|-----------------|
| 4 | Validation error | 422 | Route avec payload (POST, PATCH, PUT) — **un seul test** avec body vide pour vérifier que la validation se déclenche. Le détail des règles est testé unitairement sur la Request. |
| 5+ | Not found | 404 | Route avec paramètre de ressource (UUID invalide ou inexistant) |
| 5+ | Conflict / Business error | 4XX | Règle métier spécifique |

### Client HTTP obligatoire

**Ne jamais appeler `$this->get()`, `$this->post()`, etc. directement.** Toujours passer par `getClient()` (non authentifié) ou `getLoggedClient()` (authentifié) avant chaque appel HTTP. Le client se reset après chaque requête.

```php
// Non authentifié
$this->getClient()->post('/auth/login', $payload);

// Authentifié (user seedé)
$this->getLoggedClient(['email' => 'user@example.com'])->get('/users/me');

// Authentifié (user créé à la volée)
$this->getLoggedClient()->delete("/users/{$user->id}/{entités}/{$entity->id}");
```

### Template

```php
namespace Tests\Feature\Domain\{Domaine};

use App\Domain\User\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\TestFeatureCase;

class {Entité}ControllerTest extends TestFeatureCase
{
    // --- CREATE ---

    public function testPostCreateReturnsCreated(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->getLoggedClient(['email' => $user->email])->post("/users/{$user->id}/{entités}", $this->validCreateBody());

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testPostCreateWithoutAccessTokenReturnsUnauthorized(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->getClient()->post("/users/{$user->id}/{entités}", $this->validCreateBody());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPostCreateWithOtherUserReturnsForbidden(): void
    {
        $owner = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->getLoggedClient()->post("/users/{$owner->id}/{entités}", $this->validCreateBody());

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPostCreateWithEmptyBodyReturnsUnprocessableEntity(): void
    {
        $user = User::where('email', 'user@example.com')->firstOrFail();

        $response = $this->getLoggedClient(['email' => $user->email])->post("/users/{$user->id}/{entités}", []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // --- SHOW ---

    public function testGetShowReturnsSuccess(): void
    {
        // ...
    }

    public function testGetShowWithoutAccessTokenReturnsUnauthorized(): void
    {
        // ...
    }

    public function testGetShowWithOtherUserReturnsForbidden(): void
    {
        // ...
    }

    public function testGetShowWithInvalidIdReturnsNotFound(): void
    {
        // ...
    }

    // --- Helpers (après tous les tests) ---

    /** @return array<string, mixed> */
    private function validCreateBody(): array
    {
        return [
            'name' => 'Test',
        ];
    }
}
```

### Nommage

- Format : `test{Method}{Action}With{Condition}Returns{HttpCode}`
- La partie `With{Condition}` est optionnelle (omise pour le cas nominal).
- Succès : `testPostCreateReturnsCreated`, `testGetIndexReturnsSuccess`
- Erreur : `testPostCreateWithoutAccessTokenReturnsUnauthorized`, `testPostCreateWithEmptyBodyReturnsUnprocessableEntity`, `testGetShowWithInvalidIdReturnsNotFound`, `testPostLogoutWithAlreadyUsedTokenReturnsUnauthorized`

### Assertions recommandées

```php
// Code HTTP
$response->assertStatus(Response::HTTP_CREATED);

// Structure JSON
$response->assertJsonStructure(['id', 'name']);

// Valeur JSON
$response->assertJson(['name' => 'Test']);

// État DB
$this->assertDatabaseHas('{table}', ['name' => 'Test']);
$this->assertDatabaseMissing('{table}', ['name' => 'Test']);
$this->assertDatabaseCount('{table}', 1);
```

## Tests unitaires (Service)

### Règle de couverture

Chaque méthode publique du service doit être testée. Pour chaque méthode :

1. **Cas nominal** : le chemin heureux retourne le résultat attendu.
2. **Cas d'exception** : chaque `catch`, condition d'erreur, ou throw.
3. **Chaque branche** : chaque `if/else`, `match`, condition ternaire.

### Template

```php
namespace Tests\Unit\Domain\{Domaine}\Services;

use App\Domain\{Domaine}\DTOs\Create{Entité}DTO;
use App\Domain\{Domaine}\Models\{Entité};
use App\Domain\{Domaine}\Repositories\{Entité}Repository;
use App\Domain\{Domaine}\Services\Create{Entité}Service;
use App\Domain\{Domaine}\Services\Create{Entité}ServiceInterface;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class Create{Entité}ServiceTest extends TestUnitCase
{
    public function testCreateReturns{Entité}(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $dto = $this->getCreate{Entité}DTO();
        $entity = $this->get{Entité}Mock();
        $repository = $this->get{Entité}Repository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($entity);

        $service = $this->getCreate{Entité}Service($repository);
        $result = $service->create($dto);

        $this->assertSame($entity, $result);
    }

    public function testCreateThrowsAndRollsBack(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        $dto = $this->getCreate{Entité}DTO();
        $repository = $this->get{Entité}Repository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willThrowException(new \RuntimeException('DB error'));

        $service = $this->getCreate{Entité}Service($repository);

        $this->expectException(\RuntimeException::class);
        $service->create($dto);
    }

    // --- 1. Service testé ---

    private function getCreate{Entité}Service(
        ?{Entité}Repository $repository = null,
    ): Create{Entité}ServiceInterface {
        return new Create{Entité}Service(
            $repository ?? $this->createStub({Entité}Repository::class),
        );
    }

    // --- 2. Dépendances du constructeur ---

    private function get{Entité}Repository(): {Entité}Repository&MockObject
    {
        return $this->createMock({Entité}Repository::class);
    }

    // --- 3. DTOs ---

    private function getCreate{Entité}DTO(): Create{Entité}DTO&MockObject
    {
        return $this->createMock(Create{Entité}DTO::class);
    }

    // --- 4. Models ---

    private function get{Entité}Mock(
        string $id = 'fake-uuid',
    ): {Entité}&MockObject {
        return $this->createConfiguredModelMock({Entité}::class, [
            'id' => $id,
        ]);
    }
}
```

### Ordre des méthodes helper

1. Méthode du service testé (ex: `getCreate{Entité}Service()`)
2. Dépendances du constructeur du service (ex: `get{Entité}Repository()`)
3. DTOs (ex: `getCreate{Entité}DTO()`)
4. Models (ex: `get{Entité}Mock()`)

### Convention des méthodes helper

- **Toute entité** (model, DTO, repository, service) utilisée dans les tests **doit** avoir sa propre méthode helper privée.
- Les **paramètres** des méthodes helper utilisent le **type simple** (`?{Entité}Repository`, `?string`), jamais le type intersection.
- Les **retours** des méthodes helper de mock utilisent le **type intersection** (`{Entité}&MockObject`).
- La méthode du **service testé** prend ses dépendances en **paramètres nullable** (`?{Entité}Repository $repository = null`). Si un paramètre n'est pas fourni, il est remplacé par un `createStub()` (stub sans attente).
- Les **DTOs** sont mockés via `createMock()`.
- Les **Models** sont mockés via `createConfiguredModelMock()` avec tous les attributs en **paramètres avec valeur par défaut**.
- Les **Repositories** et autres dépendances sont mockés via `createMock()` quand on a besoin de vérifier les appels (`expects`), sinon via `createStub()`.

## Tests unitaires (DTO)

### Règle de couverture

Tester `toArray()` pour vérifier :
- Le mapping des propriétés vers les clés DB.
- Le filtrage des valeurs nulles (les champs null ne doivent pas apparaître).
- Les cas avec tous les champs remplis et avec des champs partiels.

### Template

```php
namespace Tests\Unit\Domain\{Domaine}\DTOs;

use App\Domain\{Domaine}\DTOs\Create{Entité}DTO;
use Tests\Unit\TestUnitCase;

class Create{Entité}DTOTest extends TestUnitCase
{
    public function testToArrayReturnsAllFields(): void
    {
        $dto = new Create{Entité}DTO(
            name: 'Test',
            userId: 'fake-uuid',
        );

        $this->assertSame([
            'name' => 'Test',
            'user_id' => 'fake-uuid',
        ], $dto->toArray());
    }
}
```

```php
class Update{Entité}DTOTest extends TestUnitCase
{
    public function testToArrayFiltersNullValues(): void
    {
        $model = $this->createConfiguredModelMock({Entité}::class);
        $dto = new Update{Entité}DTO(model: $model);

        $this->assertSame([], $dto->toArray());
    }

    public function testToArrayReturnsOnlySetFields(): void
    {
        $model = $this->createConfiguredModelMock({Entité}::class);
        $dto = new Update{Entité}DTO(model: $model, name: 'Updated');

        $this->assertSame(['name' => 'Updated'], $dto->toArray());
    }

    public function testGetModelReturnsModel(): void
    {
        $model = $this->createConfiguredModelMock({Entité}::class, ['id' => 'fake-uuid']);
        $dto = new Update{Entité}DTO(model: $model);

        $this->assertSame($model, $dto->getModel());
    }
}
```

## Tests unitaires (Request)

### Règle

Les règles de validation des Requests sont testées **unitairement**, pas via les tests d'intégration (trop complexe et lent). On vérifie que `rules()` retourne les bonnes règles et que `messages()` retourne les bons messages.

### Template

```php
namespace Tests\Unit\Domain\{Domaine}\Http\Requests;

use App\Domain\{Domaine}\Http\Requests\Create{Entité}Request;
use Tests\Unit\TestUnitCase;

class Create{Entité}RequestTest extends TestUnitCase
{
    public function testRulesContainsRequiredFields(): void
    {
        $request = new Create{Entité}Request();
        $rules = $request->rules();

        $this->assertArrayHasKey('name', $rules);
    }

    public function testRulesNameIsRequired(): void
    {
        $request = new Create{Entité}Request();
        $rules = $request->rules();

        $this->assertContains('required', $rules['name']);
    }

    public function testMessagesContainsAllRuleKeys(): void
    {
        $request = new Create{Entité}Request();
        $messages = $request->messages();

        $this->assertArrayHasKey('name.required', $messages);
        $this->assertArrayHasKey('name.string', $messages);
    }
}
```

### Cas à couvrir

- Chaque champ présent dans `rules()` a ses règles vérifiées.
- `requiredRules()` : vérifier que `required` est bien ajouté.
- `optionnalRules()` (pour les Update) : vérifier que `sometimes` est ajouté à la place de `required`.
- `messages()` : vérifier que chaque combinaison `{field}.{rule}` a son message i18n.

## Checklist de tests pour un domaine

1. [ ] `tests/Unit/Domain/{Domaine}/Services/Create{Entité}ServiceTest.php` (et un fichier par service) — chaque branche
2. [ ] `tests/Unit/Domain/{Domaine}/DTOs/Create{Entité}DTOTest.php` — `toArray()`
3. [ ] `tests/Unit/Domain/{Domaine}/DTOs/Update{Entité}DTOTest.php` — `toArray()`, filtrage nulls, `getModel()`
4. [ ] `tests/Unit/Domain/{Domaine}/Http/Requests/Create{Entité}RequestTest.php` — `rules()`, `messages()`
5. [ ] `tests/Unit/Domain/{Domaine}/Http/Requests/Update{Entité}RequestTest.php` — `rules()`, `messages()`
6. [ ] `tests/Feature/Domain/{Domaine}/{Entité}ControllerTest.php` — chaque route : 401, 403, 2XX + 422/404 selon contexte
7. [ ] Vérifier la couverture : chaque `if`, `match`, `try/catch` a son test

## Procédure d'exécution

1. Lire le code source du composant à tester pour identifier toutes les branches.
2. Lister les cas de test nécessaires (un par branche + cas nominaux).
3. Écrire les tests en suivant les templates ci-dessus.
4. Lancer `make test` pour vérifier que tous les tests passent.
5. Si un cas semble inatteignable, **ne pas le supprimer** — demander à l'utilisateur pourquoi ce cas existe.

## Clean Code

Une fois les tests terminés, invoquer le skill `/clean-code` pour lancer le formatage et l'analyse statique.
