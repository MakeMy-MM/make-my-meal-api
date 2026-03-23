---
description: Enforce the following Domain-Driven Design constraints and aggregate boundaries strictly during all architectural planning, code generation, and refactoring tasks.
---

## Concepts

### Aggregate Root

Un **Aggregate Root** est le model principal d'un domaine. Point d'entrée unique pour accéder et modifier un ensemble cohérent d'entités liées.

### Entité enfant

Une **entité enfant** est un model qui n'a pas de sens métier sans son Aggregate Root. Toujours manipulée à travers son parent.

## Identification

| Critère | Aggregate Root | Entité enfant |
|---------|---------------|---------------|
| Existe seul | Oui | Non (dépend du parent) |
| A son propre Repository | Oui | Non |
| A son propre Service | Oui | Non (géré par le Service du parent) |
| A ses propres routes | Oui (racine ou imbriquées) | Non (sous-routes du parent uniquement) |
| Suppression | Indépendante ou soft delete | `cascadeOnDelete()` avec le parent |
| Timestamps | Oui | Non (`$timestamps = false`) |
| `OwnerInterface.getOwner()` | Retourne directement le User | Délègue au parent |

## Règles

- Seuls les **Aggregate Roots** ont un Repository et des Services.
- Les entités enfants sont gérées via le Repository et les Services de l'Aggregate Root.
- Les entités enfants sont accessibles uniquement via des **sous-routes** de l'Aggregate Root.
- Les entités enfants ont leurs propres DTOs et Inputs, placés dans le domaine de l'Aggregate Root.
- Les entités enfants sont dans le dossier `Models/` de l'Aggregate Root, pas dans un domaine séparé.
- Les opérations modifiant l'Aggregate Root **et** ses entités enfants doivent être dans une **seule transaction**.
- Un Controller d'entité enfant appelle toujours le **Service de l'Aggregate Root**.
