---
description: Enforce the following formatting, structural, and tooling rules strictly during all PHP code generation, refactoring, and review tasks.
---

# Code Style

## Outils de qualité

### PHP-CS-Fixer

- Configuré dans `.php-cs-fixer.dist.php`.
- Base : `@PER-CS`.
- S'applique sur : `app/`, `config/`, `database/`, `routes/`, `tests/`.

### PHPStan

- Configuré dans `phpstan.neon`.
- Niveau : **8** (strictness maximale).
- Extensions : Larastan, Carbon.
- Exclut les base classes de tests (`*TestCase.php`).
- Règle : `noEnvCallsOutsideOfConfig: true` — les appels `env()` sont interdits en dehors de `config/`.

### EditorConfig

- UTF-8, LF, indentation 4 espaces, newline final, trim trailing whitespace.
- YAML : indentation 2 espaces (sauf `compose.yaml` : 4 espaces).

## Règles de style PHP

### Accolades (Allman modifié)

- **Classes et fonctions** : accolade ouvrante sur la **ligne suivante**.
- **Structures de contrôle** (`if`, `for`, `while`, etc.) : accolade ouvrante sur la **même ligne**.
- **Fonctions anonymes (closures)** : accolade ouvrante sur la **même ligne**.
- **Classes anonymes** : accolade ouvrante sur la **ligne suivante**.

```php
// Classe / Fonction : next line
class UserService
{
    public function create(CreateUserDTO $dto): User
    {
        if ($dto->email === null) { // Structure de contrôle : same line
            throw new \InvalidArgumentException();
        }

        return $this->repository->create($dto);
    }
}
```

### Virgules trailing

- Obligatoires en multiline pour : **arrays**, **arguments**, **paramètres**.

```php
$data = [
    'name' => 'Tomate',
    'unit' => 'kg',
];

$this->service->create(
    email: $email,
    password: $password,
);
```

### Imports et espaces

- `no_unused_imports` : les imports inutilisés sont supprimés.
- `ordered_imports` : triés par ordre alphabétique.
- `fully_qualified_strict_types` : types entièrement qualifiés pour les strict types.
- `concat_space` : un espace autour de l'opérateur de concaténation (`. `).
- `not_operator_with_successor_space` : **pas d'espace** après `!` (`!$value`).
- `blank_line_before_statement` : ligne vide avant chaque `return`.
- `multiline_whitespace_before_semicolons` : le `;` est sur sa propre ligne dans les appels chaînés multi-lignes.

```php
// Chaîne fluide multi-ligne : ; sur nouvelle ligne
return (new UserResource($user))
    ->additional([...])
    ->response()
    ->setStatusCode(Response::HTTP_CREATED)
;
```

### Casing des tests

- Les méthodes de tests PHPUnit utilisent le **camelCase** (`testCreateReturnsIngredient`).

## Assertions avec webmozart/assert

- La librairie `webmozart/assert` est utilisée pour les assertions de type et de valeur dans le code applicatif (pas seulement les tests).
- Utiliser `Assert::notNull()`, `Assert::isInstanceOf()`, etc. pour valider les pré-conditions.

```php
use Webmozart\Assert\Assert;

Assert::notNull($this->email);
Assert::isInstanceOf($this->collection, Collection::class);
```
