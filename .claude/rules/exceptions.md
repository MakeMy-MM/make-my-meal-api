---
description: Enforce the following exception handling conventions, HTTP status code mappings, and error rendering rules strictly during all API development, error handling, and debugging tasks.
---

# Convention Exceptions

## Exceptions Custom

Éléments globaux dans `app/Http/Exceptions/`. Étendent `HttpException` de Symfony.

| Exception | Code HTTP | Message par défaut | Usage |
|-----------|-----------|-------------------|-------|
| `InternalServerErrorHttpException` | 500 | `Internal Server Error` | Wrapper les erreurs d'accès aux données dans les Repositories |
| `NotImplementedHttpException` | 501 | `Not Implemented` | Fonctionnalité non encore implémentée |
| `UnauthorizedHttpException` | 401 | `Unauthorized` | Accès non authentifié |

## Rendering dans bootstrap/app.php

Configuré via `withExceptions()` :

- `EntityNotFoundException` → `NotFoundHttpException` (404).
- `UnexpectedValueException`, `LogicException`, `InternalServerErrorHttpException` → loguées en `emergency` puis converties en `InternalServerErrorHttpException` (500).

## Règles

- Les Repositories wrappent les exceptions d'accès aux données dans `InternalServerErrorHttpException`.
- Les exceptions métier spécifiques à un domaine sont placées dans `app/Domain/{Domaine}/Exceptions/`.
- Toute nouvelle exception HTTP doit être documentée dans ce fichier.
