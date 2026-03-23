---
description: Enforce the following RESTful API design constraints, naming conventions, and validation rules strictly during all routing, controller implementation, and API documentation tasks.
---

## Verbes HTTP

- `GET` : lecture, ne modifie rien.
- `POST` : création d'une ou plusieurs resources.
- `PUT` : remplacement complet d'une ressource.
- `PATCH` : modification partielle d'une ou plusieurs resources.
- `DELETE` : suppression d'une ou plusieurs ressources.

## Codes de Réponse

- Les codes de réponse HTTP sont utilisés pour indiquer le résultat d'une requête.
- Tout code d'erreur non listé ici devra être justifié et documenté avec un commentaire.

### 2XX : requête traité avec succès

- `200` : renvoie d'un contenu dépendant de la requête.
- `201` : création d'une resource.
- `204` : aucun contenu de retour.

### 4XX : erreur côté client

- `400` : syntaxe erronée.
- `401` : non authentifié.
- `403` : authentifié mais non autorisé.
- `404` : ressource introuvable.
- `422` : erreur de validation.
- `429` : trop de requêtes dans un court délai.

### 5XX : erreur côté serveur

- `500` : erreur serveur interne.
- `501` : fonctionnalité non implémentée.
- `503` : service indisponible (maintenance, surcharge, etc.).

## Nomenclature

### Routes

- Utilisation du kebab-case pour le nom des routes (ex: `/long-name-resources`).
- Les ressources sont toujours au pluriel (ex: `/resources`).
- Les ressources imbriquées reflètent les relations de possession : `/resources/{id}/sub-resources/{sub-id}`.
- Les routes à action spécifique utilisent un suffixe d'action non ambigu (ex: `/resources/{id}/activate` pour activer un utilisateur).

### Paramètres de requête

- Utilisation du snake_case pour les noms de paramètres de requête (ex: `?params_name=`).
- La pagination utilise des paramètres standard : `?per_page=50` (nombre d'éléments), `?page=2` (numéro de page), `?limit=50` (nombre d'éléments), `?offset=100` (décalage).
- Les filtres utilisent des paramètres de resource.
- Les champs de tri utilisent un paramètre `sort` avec des préfixes pour l'ordre : `?sort=field` (tri ascendant), `?sort=-field` (tri descendant).

### Champs dans le payload

- Utilisation du snake_case pour les noms de champs dans le payload (ex: `{ 'params_name': 'params' }`).

## Versioning

- La version de l'API est indiquée dans l'URL : `/api/v1/resources`.
- L'incrémentation de la version se fait lors de changements majeurs sur la route ou la structure de la réponse.
- La dépréciation d'une version doit être annoncée au moins quelques mois à l'avance avec l'alternative proposée (si elle existe).

## Validation des paramètres de route

- Les paramètres de route UUID sont validés globalement via `RoutePatterns` (`app/Utils/RoutePatterns.php`).
- `RoutePatterns::getPatterns()` retourne un tableau associatif `nom_paramètre => regex_uuid`.
- Appelé dans `routes/api.php` via `Route::patterns(RoutePatterns::getPatterns())`.
- Chaque nouveau model avec un paramètre de route UUID doit être ajouté dans `RoutePatterns::getPatterns()`.
