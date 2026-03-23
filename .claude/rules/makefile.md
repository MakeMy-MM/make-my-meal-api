---
description: Enforce the following Makefile command utilization and execution context constraints strictly during all terminal operations, script generation, and environment interaction tasks. Never execute raw PHP or Artisan commands outside the provided Makefile abstractions.
---

## Règles générales

- Toutes les commandes s'exécutent via Docker (`docker compose exec app`). Ne jamais lancer les commandes PHP directement sur la machine hôte.

## Docker

| Commande | Description |
|----------|-------------|
| `make up` | Démarre les containers (app, pgsql, redis) avec Xdebug activé |
| `make up-build` | Démarre les containers avec rebuild de l'image |
| `make down` | Stoppe les containers |
| `make restart` | Redémarre les containers |
| `make logs` | Affiche les logs du container app en temps réel |
| `make bash` | Ouvre un shell dans le container app |

## Composer

| Commande | Description |
|----------|-------------|
| `make install` | Installe les dépendances Composer |

## Database

| Commande | Description |
|----------|-------------|
| `make migrate` | Exécute les migrations |
| `make migrate-rollback` | Rollback la dernière migration |
| `make migrate-refresh` | Rollback + re-run de toutes les migrations |
| `make seed` | Exécute les seeders |
| `make fresh` | Drop + re-create toutes les tables + seed |

## Artisan

| Commande | Description |
|----------|-------------|
| `make artisan cmd="commande"` | Exécute une commande artisan arbitraire |

## Tests

| Commande | Description |
|----------|-------------|
| `make test` | Lance tous les tests (Xdebug off) |
| `make test-debug` | Lance tous les tests avec Xdebug activé |

## Code quality

| Commande | Description |
|----------|-------------|
| `make format` | Corrige le formatage via php-cs-fixer |
| `make format-fix` | Vérifie le formatage sans corriger (dry-run + diff) |
| `make analyse` | Analyse statique PHPStan (niveau 8) |

## Hooks

| Commande | Description |
|----------|-------------|
| `make hooks` | Configure git pour utiliser le dossier `hooks/` comme hooksPath |

## Pre-commit hook

Le hook `hooks/pre-commit` est exécuté automatiquement avant chaque commit. Il lance :
1. `make format-fix` — vérifie le formatage (bloque si non conforme).
2. `make analyse` — vérifie l'analyse statique (bloque si erreurs).

Pour activer les hooks : `make hooks`.
