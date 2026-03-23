---
name: code
description: Guide procédural pour créer des composants dans l'API (model, service, repository, DTO, controller, request, resource, test, migration, exception). A invoquer lors de l'implémentation d'une fonctionnalité.
user-invocable: true
---

# Skill: Code

Guide procédural pour la création de composants. Les contraintes et conventions sont définies dans les rules — ce skill décrit **comment** construire chaque composant.

## Flow d'une route

```
Request → Controller → Input (fromRequest) → Service (via interface) → Repository → Model
```

1. Le **Controller** reçoit la Request, crée l'Input via `fromRequest()`, appelle le Service, et construit la Resource.
2. Le **Service** contient la logique métier, gère les transactions DB, et retourne des Models ou Collections.
3. Le **Controller** construit les Resources à partir des Models retournés.

## Templates par composant

### Model

```php
namespace App\Domain\{Domaine}\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\User\Models\User;
use App\Models\OwnerInterface;

class {Entité} extends Model implements OwnerInterface
{
    use HasUuids;

    protected $table = '{table}';

    protected $fillable = [
        // champs
    ];

    protected $hidden = [];

    protected $casts = [];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getOwner(): User
    {
        return $this->user;
    }
}
```

- Overrider `newFactory()` si le model n'est pas dans `App\Models\`.
- `$timestamps = false` sur les entités enfants (RecipeIngredient, RecipeStep).

### Service (un par action)

Nommage : `{Action}{Entité}Service` + `{Action}{Entité}ServiceInterface`.

**Interface :**

```php
namespace App\Domain\{Domaine}\Services;

interface Create{Entité}ServiceInterface
{
    public function create(Create{Entité}DTO $dto): {Entité};
}
```

**Implémentation :**

```php
namespace App\Domain\{Domaine}\Services;

use Illuminate\Support\Facades\DB;

class Create{Entité}Service implements Create{Entité}ServiceInterface
{
    public function __construct(
        private readonly {Entité}Repository $repository,
    ) {}

    public function create(Create{Entité}DTO $dto): {Entité}
    {
        DB::beginTransaction();

        try {
            $result = $this->repository->create($dto);
        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        DB::commit();

        return $result;
    }
}
```

- Un service = une action = une responsabilité unique.
- Toujours wrapper les écritures dans `beginTransaction / commit / rollBack`.

### Repository

```php
namespace App\Domain\{Domaine}\Repositories;

use App\Domain\{Domaine}\Models\{Entité};
use App\DTOs\BaseFieldDTO;
use App\Http\Exceptions\InternalServerErrorHttpException;
use App\Repositories\ModelRepository;
use Illuminate\Support\Collection;

/**
 * @extends ModelRepository<{Entité}>
 *
 * @method Collection<int, {Entité}> findByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 * @method {Entité}|null              findFirstByFields(BaseFieldDTO $data, array<string> $with = [], array<string, string> $orderBy = [])
 */
class {Entité}Repository extends ModelRepository
{
    public function create(Create{Entité}DTO $dto): {Entité}
    {
        try {
            $entity = new {Entité}();
            $entity->fill($dto->toArray());
            $entity->save();

            return $entity;
        } catch (\Throwable $e) {
            throw new InternalServerErrorHttpException(previous: $e);
        }
    }
}
```

- Wrapper les erreurs dans `InternalServerErrorHttpException`.
- Re-déclarer les méthodes héritées via `@method` avec le type concret.

### DTOs

**Fields{Entité}DTO** (tous les champs possibles, nullable) :

```php
namespace App\Domain\{Domaine}\DTOs;

use App\DTOs\BaseFieldDTO;

class Fields{Entité}DTO extends BaseFieldDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $userId = null,
    ) {}

    /** @return array<string, mixed> */
    protected function getProperties(): array
    {
        return [
            'name' => $this->name,
            'user_id' => $this->userId,
        ];
    }
}
```

**Create{Entité}DTO** (champs requis) :

```php
class Create{Entité}DTO extends Fields{Entité}DTO
{
    public function __construct(
        string $name,
        string $userId,
    ) {
        parent::__construct(
            name: $name,
            userId: $userId,
        );
    }
}
```

**Update{Entité}DTO** (model + champs optionnels) :

```php
use App\DTOs\UpdateDTOInterface;

class Update{Entité}DTO extends Fields{Entité}DTO implements UpdateDTOInterface
{
    public function __construct(
        private readonly {Entité} $model,
        ?string $name = null,
    ) {
        parent::__construct(name: $name);
    }

    public function getModel(): {Entité}
    {
        return $this->model;
    }
}
```

- Les DTOs d'action d'autres domaines étendent le `Fields{Entité}DTO` du domaine du model.

### Input

```php
namespace App\Domain\{Domaine}\Inputs;

use App\Domain\{Domaine}\DTOs\Create{Entité}DTO;
use App\Inputs\InputInterface;
use Illuminate\Http\Request;

class Create{Entité}Input extends Create{Entité}DTO implements InputInterface
{
    /** @param array<string, mixed> $models */
    public static function fromRequest(Request $data, array $models = []): self
    {
        return new self(
            name: $data->validated('name'),
            userId: $models['user']->id,
        );
    }
}
```

### Controller

```php
namespace App\Domain\{Domaine}\Http\Controllers;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class {Entité}Controller extends Controller
{
    public function __construct(
        private readonly Create{Entité}ServiceInterface $createService,
        private readonly Update{Entité}ServiceInterface $updateService,
    ) {}

    public function create(Create{Entité}Request $request, User $user): \Illuminate\Http\JsonResponse
    {
        $input = Create{Entité}Input::fromRequest($request, ['user' => $user]);
        $entity = $this->createService->create($input);

        return (new {Entité}Resource($entity))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED)
        ;
    }
}
```

**Controller Auth (login/register) :**

```php
public function login(LoginRequest $request): JsonResponse
{
    $input = LoginInput::fromRequest($request, []);
    $user = $this->loginService->login($input);
    $tokenDTO = $this->tokenService->create($user);

    return (new UserResource($user))
        ->additional([TokenResource::$wrap => (new TokenResource($tokenDTO))->resolve()])
        ->response()
        ->setStatusCode(Response::HTTP_OK)
    ;
}

public function refresh(RefreshRequest $request): JsonResponse
{
    // ... validation du refresh token ...

    return (new TokenResource($tokenDTO))
        ->nowrap()
        ->response()
        ->setStatusCode(Response::HTTP_OK)
    ;
}
```

- Le controller injecte les **interfaces** des services, pas les implémentations.
- Un service par action injecté dans le constructeur.
- Les tokens (access + refresh) sont créés via `TokenServiceInterface::create()` qui retourne un `TokenDTO`.
- Les tokens sont ajoutés via `->additional([TokenResource::$wrap => new TokenResource($tokenDTO)])`.
- L'endpoint `/refresh` retourne uniquement `TokenResource` avec `->nowrap()` (pas de `UserResource`).

### Request

```php
namespace App\Domain\{Domaine}\Http\Requests;

use App\Http\Requests\PublicRequest;
use App\Domain\{DomaineModel}\Enums\{Entité}RequestRule;
use Illuminate\Contracts\Validation\ValidationRule;

class Create{Entité}Request extends PublicRequest
{
    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            {Entité}RequestRule::NAME->value => $this->requiredRules({Entité}RequestRule::NAME->rules()),
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return array_merge(
            {Entité}RequestRule::NAME->messages(),
        );
    }
}
```

- `PublicRequest` pour les routes publiques, `RoleRequest` pour les routes authentifiées.
- `requiredRules()` pour create, `optionnalRules()` pour update.
- Les enums de validation sont dans le domaine du **model**, pas du domaine de la request.

### Enum de validation

```php
namespace App\Domain\{Domaine}\Enums;

use App\Enums\RuleRequestInterface;

enum {Entité}RequestRule: string implements RuleRequestInterface
{
    case NAME = 'name';

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return match ($this) {
            self::NAME => [
                'name' => ['string', 'max:255'],
            ],
        };
    }

    /** @return array<string, string> */
    public function messages(string $prefix = ''): array
    {
        return match ($this) {
            self::NAME => [
                $prefix . 'name.required' => '{domaine}.name.required',
                $prefix . 'name.string' => '{domaine}.name.string',
                $prefix . 'name.max' => '{domaine}.name.max',
            ],
        };
    }
}
```

### Resource

```php
namespace App\Domain\{Domaine}\Http\Resources;

use App\Http\Resources\BasicResource;

/** @mixin \App\Domain\{Domaine}\Models\{Entité} */
class {Entité}Resource extends BasicResource
{
    public ?string $wrap = '{entité}';

    /** @return array<string, mixed> */
    public function toArray(\Illuminate\Http\Request $request): array
    {
        return [
            'id' => $this->id,
        ];
    }
}
```

### Provider

```php
namespace App\Domain\{Domaine}\Providers;

use Illuminate\Support\ServiceProvider;

class {Domaine}ServiceProvider extends ServiceProvider
{
    public array $bindings = [
        Create{Entité}ServiceInterface::class => Create{Entité}Service::class,
        Update{Entité}ServiceInterface::class => Update{Entité}Service::class,
        Delete{Entité}ServiceInterface::class => Delete{Entité}Service::class,
    ];
}
```

- Enregistrer dans `bootstrap/providers.php`.

### Migration

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{table}', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{table}');
    }
};
```

- Ajouter le paramètre de route dans `RoutePatterns::getPatterns()`.

### Exception custom

```php
namespace App\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class {Nom}HttpException extends HttpException
{
    public function __construct(
        string $message = '{Message par défaut}',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = [],
    ) {
        parent::__construct(Response::HTTP_{CODE}, $message, $previous, $headers, $code);
    }
}
```

- Ajouter le rendering dans `bootstrap/app.php` si nécessaire.

### Routes

```php
// routes/versionning/v1/{domaine}.php
use Illuminate\Support\Facades\Route;

Route::prefix('{domaine-pluriel}')->group(function () {
    Route::post('/', [{Entité}Controller::class, 'create']);
    Route::get('/', [{Entité}Controller::class, 'index']);
});
```

- Inclure le fichier dans `routes/versionning/v1/routes.php`.

### Seeder (fixture de test)

Chaque model a son propre seeder. Les seeders servent de fixtures pour les tests d'intégration (`TestFeatureCase` a `$seed = true`).

```php
namespace Database\Seeders;

use App\Domain\{Domaine}\Models\{Entité};
use Illuminate\Database\Seeder;

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

## Clean Code

Une fois l'implémentation terminée, invoquer le skill `/clean-code` pour lancer le formatage et l'analyse statique.

## Tests

Une fois le clean code terminé, demander à l'utilisateur si tout est bon pour passer aux tests. Si l'utilisateur demande des modifications, les appliquer, relancer le clean code, puis redemander si c'est bon pour passer aux tests. Répéter jusqu'à validation. Une fois validé, invoquer le skill `/code-test`.

## Création d'un Aggregate Root avec entités enfants

Quand l'entité a des enfants (ex: Recipe avec RecipeStep) :

### Repository de l'Aggregate Root

```php
class RecipeRepository extends ModelRepository
{
    public function createStep(Recipe $recipe, CreateRecipeStepDTO $dto): RecipeStep
    {
        try {
            $step = new RecipeStep();
            $step->fill($dto->toArray());
            $step->recipe()->associate($recipe);
            $step->save();

            return $step;
        } catch (\Throwable $e) {
            throw new InternalServerErrorHttpException(previous: $e);
        }
    }
}
```

### Service avec transaction multi-entités

```php
public function createWithSteps(CreateRecipeDTO $recipeDTO, array $stepsDTO): Recipe
{
    DB::beginTransaction();

    try {
        $recipe = $this->repository->create($recipeDTO);
        foreach ($stepsDTO as $stepDTO) {
            $this->repository->createStep($recipe, $stepDTO);
        }
    } catch (\Throwable $e) {
        DB::rollBack();

        throw $e;
    }

    DB::commit();

    return $recipe;
}
```

### Routes imbriquées

```php
Route::prefix('recipes')->group(function () {
    Route::post('/', [RecipeController::class, 'create']);
    Route::get('/{recipe}', [RecipeController::class, 'show']);

    // Entités enfants : toujours sous l'Aggregate Root
    Route::prefix('{recipe}/steps')->group(function () {
        Route::post('/', [RecipeStepController::class, 'create']);
        Route::patch('/{step}', [RecipeStepController::class, 'update']);
        Route::delete('/{step}', [RecipeStepController::class, 'delete']);
    });
});
```

## Checklist de création d'un nouveau domaine

1. [ ] Model dans `app/Domain/{Domaine}/Models/`
2. [ ] Migration dans `database/migrations/`
3. [ ] Factory dans `database/factories/`
4. [ ] Seeder dans `database/seeders/` + enregistrement dans `DatabaseSeeder`
5. [ ] DTO (Fields, Create, Update) dans `app/Domain/{Domaine}/DTOs/`
5. [ ] Repository dans `app/Domain/{Domaine}/Repositories/`
6. [ ] Services + Interfaces dans `app/Domain/{Domaine}/Services/` (un par action)
7. [ ] Provider dans `app/Domain/{Domaine}/Providers/` + enregistrement dans `bootstrap/providers.php`
8. [ ] Enum de validation dans `app/Domain/{Domaine}/Enums/`
9. [ ] Request(s) dans `app/Domain/{Domaine}/Http/Requests/`
10. [ ] Input(s) dans `app/Domain/{Domaine}/Inputs/`
11. [ ] Controller dans `app/Domain/{Domaine}/Http/Controllers/`
12. [ ] Resource dans `app/Domain/{Domaine}/Http/Resources/`
13. [ ] Routes dans `routes/versionning/v1/{domaine}.php` + inclusion dans `routes.php`
14. [ ] RoutePatterns : ajouter le paramètre UUID
15. [ ] Tests unitaires dans `tests/Unit/Domain/{Domaine}/`
16. [ ] Tests d'intégration dans `tests/Feature/Domain/{Domaine}/`
17. [ ] DOMAINS.md : ajouter le domaine
