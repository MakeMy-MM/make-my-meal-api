---
name: commit
description: Permet de créer un commit en respectant une convention. A utiliser au moment de créer un commit.
user-invocable: true
---

# Skill: Commit

## Format du message

```
<type>(<scope>)[!]: <description>

[body]
```

- **type** : obligatoire, en minuscules.
- **scope** : obligatoire, entre parenthèses, décrit la section du code concernée (ex: `global`, `auth`, `recipe`, `ingredient`).
- **!** : optionnel, placé avant `:` pour signaler un breaking change.
- **description** : obligatoire, résumé concis en minuscules (pas de majuscule initiale, pas de point final).
- **body** : optionnel, séparé de la description par une ligne vide. Forme libre, peut contenir plusieurs paragraphes.

## Types autorisés

| Type | Description |
|------|-------------|
| `feat` | Ajout d'une nouvelle fonctionnalité |
| `fix` | Correction d'un bug |
| `refactor` | Modification du code sans changement fonctionnel ni correction de bug |
| `test` | Ajout ou modification de tests |
| `docs` | Modification de la documentation |
| `style` | Changement de formatage (espaces, virgules, etc.) sans modification de logique |
| `perf` | Amélioration des performances |
| `ci` | Modification de la configuration CI/CD |
| `build` | Modification du système de build ou des dépendances |
| `chore` | Tâche de maintenance ne touchant pas le code source ni les tests |

## Breaking Changes

Un breaking change est signalé par un `!` après le type/scope : `feat!: description` ou `feat(auth)!: description`

## Règles

1. Analyser les changements avec `git status` et `git diff --staged` avant de rédiger le message.
2. Choisir le **type** en fonction de la nature des changements (pas de la taille du diff).
3. Définir le **scope** en utilisant le nom du domaine métier concerné en minuscules (ex: `auth`, `recipe`, `user`, `ingredient`). Si les changements touchent plusieurs domaines, utiliser `global`.
4. Rédiger la **description** en anglais, en minuscules, sans point final, de manière concise et impérative (ex: `add login endpoint`, `fix token expiration`).
5. Ajouter un **body** si le "pourquoi" du changement n'est pas évident à partir de la description seule.
6. Stager uniquement les fichiers pertinents (pas de `git add -A`).
7. Ne jamais commiter de fichiers sensibles (`.env`, credentials, etc.).

## Exemples

```
feat(auth): add login endpoint
```

```
fix(recipe): handle missing ingredients in recipe creation
```

```
refactor(global): rename DTOs to follow naming convention
```

```
feat(user)!: change user ID from integer to UUID
```

```
test(auth): add unit tests for auth service
```

## Procédure d'exécution

1. Lancer `git status` et `git diff --staged` pour analyser les changements.
2. Lancer `git log --oneline -5` pour vérifier le style des commits récents.
3. Stager les fichiers pertinents par nom (`git add <fichier>`).
4. Rédiger le message de commit selon le format ci-dessus.
5. Créer le commit via `git commit` avec un HEREDOC pour le message.
6. Vérifier le résultat avec `git status`.
