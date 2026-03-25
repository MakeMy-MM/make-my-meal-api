---
description: Enforce the following API response formatting conventions, JSON structures, and error payload definitions strictly during all controller implementation, resource generation, and API documentation tasks.
---

## Classes de base

- Les Resources unitaires étendent `BasicResource` (élément global dans `app/Http/Resources/`).
- Les ResourceCollections étendent `BasicResourceCollection` (élément global dans `app/Http/Resources/`).
- `BasicResource` et `BasicResourceCollection` définissent `$wrap = null` par défaut et fournissent une méthode `nowrap()`.
- `nowrap()` est instance-level : il désactive le wrap pour cette instance sans affecter les autres (le `$wrap` statique est temporairement mis à `null` puis restauré dans `toResponse()`).
- Chaque Resource de domaine **doit** overrider `$wrap` (singulier, ex: `'ingredient'`) et `$wrapCollection` (pluriel, ex: `'ingredients'`).
- `$wrapCollection` est utilisé comme clé quand une collection de cette resource est imbriquée dans une autre resource (ex: `RecipeStepResource::$wrapCollection => RecipeStepResource::collection(...)`).
- Les ResourceCollections ne déclarent pas de `$wrap` statique. Elles initialisent `$wrap` dans le constructeur après `parent::__construct()` via `static::$wrap = Resource::$wrapCollection`.
- Quand une Resource est utilisée via `additional()` sur une autre Resource, utiliser `Resource::$wrap` comme clé et `new Resource()` pour le contenu.
- Quand une Resource est retournée directement sans wrap, appeler `->nowrap()` avant `->response()`.

## Structure de la Réponse

### Réponse de Succès

#### Resource Unique

- La réponse d'une resource unique doit être structurée de manière claire et cohérente, avec des clés explicites pour les champs de la resource et les relations éventuelles.

```json
{
    "id": "123",
    "champ": "champ_value",
    "nom_relation_resource (ex: products)": {
        "id": "456",
        "champ": "champ_value"
    }
}
```

#### Collection de Resources

- Les collections utilisent la structure `{ count, items }` via `BasicResourceCollection`.
- `count` : nombre d'éléments dans la collection.
- `items` : tableau des resources unitaires.

```json
{
    "count": 2,
    "items": [
        {
            "id": "123",
            "champ": "champ_value"
        },
        {
            "id": "789",
            "champ": "champ_value"
        }
    ]
}
```

### Réponse d'Erreur

#### Erreur Générique

- Les réponses d'erreur doivent inclure un code de statut HTTP approprié et un message d'erreur clair pour indiquer la nature de l'erreur.

```json
{
    "message": "Message describing the error"
}
```

##### Messages d'Erreur Standardisés

- `401` : `Unauthorized`
- `403` : `Forbidden`
- `404` : `[Nom de la ressource] not found`
- `429` : `Too Many Requests`

- `500` : `Internal Server Error`
- `501` : `Not Implemented`
- `503` : `Service Unavailable`

#### Erreur de validation

- Le format de validation est géré par `BaseRequest` dans `failedValidation()`.
- `pointer` : le nom du champ en erreur.
- `reason` : la première valeur du message d'erreur pour ce champ — correspond à la clé i18n définie dans les enums `RuleRequestInterface` (ex: `user.email.required`). Utilisable côté client pour la traduction.
- `message` : message statique d'erreur générique.

```json
{
    "message": "Validation error",
    "errors": [
        {
            "pointer": "email",
            "reason": "user.email.required",
            "message": "Something failed"
        }
    ]
}
```
