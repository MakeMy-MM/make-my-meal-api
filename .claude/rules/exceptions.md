---
description: Enforce the following exception handling conventions, HTTP status code mappings, and error rendering rules strictly during all API development, error handling, and debugging tasks.
---

# Convention Exceptions

## Exceptions Custom

Éléments globaux dans `app/Http/Exceptions/`. Étendent `HttpException` de Symfony.

| Exception | Code HTTP | Message par défaut | Usage |
|-----------|-----------|-------------------|-------|
| `ConflictHttpException` | 409 | `Conflict` | Violation de contrainte unique dans les Repositories |
| `InternalServerErrorHttpException` | 500 | `Internal Server Error` | Wrapper les erreurs d'accès aux données inattendues dans les Repositories |
| `NotImplementedHttpException` | 501 | `Not Implemented` | Fonctionnalité non encore implémentée |
| `UnauthorizedHttpException` | 401 | `Unauthorized` | Accès non authentifié |

## Rendering dans bootstrap/app.php

Configuré via `withExceptions()` avec des `render()` pour les exceptions spécifiques et un `respond()` comme filet de sécurité :

### Handlers `render()` (exceptions spécifiques)

1. `ModelNotFoundException` → 404 avec message `{Entity} not found`.
2. `NotFoundHttpException` → 404 (extrait le nom du model depuis le `previous` si disponible).
3. `AuthenticationException` → 401 `Unauthorized`.
4. `UniqueConstraintViolationException` → 409 `Conflict`.

### Handler `respond()` (filet de sécurité)

- Laisse passer les réponses des exceptions déjà gérées (`HttpExceptionInterface`, `AuthenticationException`, `ModelNotFoundException`, `UniqueConstraintViolationException`).
- Toute autre exception → loguée en `emergency` puis convertie en 500 `Internal Server Error`.

## Règles

- Les Repositories ne gèrent **aucune exception** — les exceptions DB remontent naturellement vers `bootstrap/app.php`.
- Les exceptions métier spécifiques à un domaine sont placées dans `app/Domain/{Domaine}/Exceptions/`.
- Toute nouvelle exception HTTP doit être documentée dans ce fichier.
- Les exceptions métier spécifiques à un domaine sont placées dans `app/Domain/{Domaine}/Exceptions/`.
- Toute nouvelle exception HTTP doit être documentée dans ce fichier.
