---
description: Enforce the following file structuring, Domain-Driven Design directory mappings, and naming conventions strictly during all component scaffolding, refactoring, and file creation tasks.
---

# Structuration des Fichiers

## Convention de Structuration

- L'arborescence se base sur le `Domain-Driven Design` (DDD) pour organiser les fichiers selon les domaines métier.
- Le dossier racine des domaines est `Domain/` (singulier).
- Chaque domaine métier est représenté par un dossier dans lequel sont regroupés tous les éléments liés à ce domaine (modèles, services, repositories, etc.).
- Les fichiers sont organisés de manière logique, en regroupant par domaine métier pour faciliter la navigation et la compréhension du code.

## Règles de Nommage

- Les noms de fichiers et de dossiers doivent être en `PascalCase` pour les classes et les dossiers, et en `camelCase` pour les fichiers de configuration ou les scripts.
- Les noms doivent être explicites et refléter clairement leur contenu ou leur fonction.
- Les fichiers de tests doivent être placés dans un dossier `tests/` à la racine du projet, avec une structure de dossiers similaire à celle des domaines métier pour faciliter la correspondance entre les fichiers de code et leurs tests.

## Structuration du dossier app

### Dossier `Domain/`

- Contient tous les domaines métier de l'application.
- Chaque domaine métier est représenté par un sous-dossier dans lequel sont regroupés tous les éléments liés à ce domaine (modèles, services, repositories, etc.).
- Exemple d'arborescence pour un domaine `User` :

```
Domain/
├── User/
│   ├── Models/
│   ├── Services/
│   ├── Repositories/
│   ├── Inputs/
│   ├── ValueObjects/
│   ├── DTOs/
│   ├── Events/
│   ├── Listeners/
│   ├── Jobs/
│   ├── Enums/
│   ├── Exceptions/
│   ├── Traits/
│   ├── Utils/
│   ├── Providers/
│   └── Http/
│       ├── Controllers/
│       ├── Requests/
│       ├── Resources/
│       └── Rules/
```

### Dossier racine `app/`

- Contient les éléments globaux de l'application qui ne sont pas spécifiques à un domaine métier.
- Ces éléments servent de base ou de contrat pour les domaines.
- Arborescence du dossier racine :

```
app/
├── DTOs/
│   ├── DTOInterface.php
│   ├── BaseFieldDTO.php
│   └── UpdateDTOInterface.php
├── Enums/
│   └── RuleRequestInterface.php
├── Http/
│   ├── Controllers/
│   │   └── Controller.php
│   ├── Exceptions/
│   │   ├── InternalServerErrorHttpException.php
│   │   ├── NotImplementedHttpException.php
│   │   └── UnauthorizedHttpException.php
│   ├── Middlewares/
│   │   └── MiddlewareInterface.php
│   ├── Requests/
│   │   ├── BaseRequest.php
│   │   ├── PublicRequest.php
│   │   └── RoleRequest.php
│   └── Resources/
│       ├── BasicResource.php
│       └── BasicResourceCollection.php
├── Inputs/
│   └── InputInterface.php
├── Models/
│   └── OwnerInterface.php
├── Providers/
│   └── AppServiceProvider.php
├── Repositories/
│   └── ModelRepository.php
├── Traits/
└── Utils/
    └── RoutePatterns.php
```

### Précision règle de nommage par dossier

- `Models/` : `User.php`, `Product.php`, etc.
- `Services/` : `CreateUserService.php`, `UpdateUserService.php`, etc. + interfaces `CreateUserServiceInterface.php`, etc. (un service par action).
- `Repositories/` : `UserRepository.php`, `ProductRepository.php`, etc.
- `Inputs/` : `CreateUserInput.php`, `UpdateUserInput.php`, etc.
- `ValueObjects/` : `Email.php`, `PhoneNumber.php`, etc.
- `DTOs/` : `FieldsUserDTO.php`, `CreateUserDTO.php`, `UpdateUserDTO.php`, etc.
- `Events/` : `UserRegistered.php`, `ProductCreated.php`, etc.
- `Listeners/` : `SendWelcomeEmail.php`, `UpdateStock.php`, etc.
- `Jobs/` : `SendEmailJob.php`, `UpdateDatabaseJob.php`, etc.
- `Enums/` : `UserRequestRule.php`, `MeasurementUnit.php`, etc.
- `Exceptions/` : `UserNotFoundException.php`, `ProductOutOfStockException.php`, etc.
- `Traits/` : `HasTimestamps.php`, `SoftDeletes.php`, etc.
- `Utils/` : `StringHelper.php`, `DateHelper.php`, etc.
- `Providers/` : `UserServiceProvider.php`, `AuthServiceProvider.php`, etc.
- `Http/Controllers/` : `UserController.php`, `ProductController.php`, etc.
- `Http/Requests/` : `CreateUserRequest.php`, `UpdateProductRequest.php`, etc.
- `Http/Resources/` : `UserResource.php`, `UserResourceCollection.php`, etc.
- `Http/Rules/` : `UserEmailRule.php`, `ProductStockRule.php`, etc.

## Carte des domaines

- Il est recommandé de maintenir une carte des domaines métier de l'application, avec une description de chaque domaine et de ses responsabilités, pour faciliter la compréhension globale de l'architecture et des interactions entre les différents domaines.
- Cette carte doit se situer à la racine du projet, dans un fichier `DOMAINS.md`, et doit être mise à jour à chaque nouveau domaine.
- Chaque domaine doit avoir une phrase d'accroche expliquant son rôle métier.

## Structuration du dossier routes

- Les fichiers de routes sont organisés par versionning dans `routes/versionning/v{n}/`.
- `routes/api.php` est un index qui inclut les fichiers de version via `require`, sans définir de prefix.
- Chaque version contient un `routes.php` qui définit son prefix (`v1`, `v2`, etc.) et inclut les fichiers thématiques via `require`.
- Chaque fichier thématique (ex: `auth.php`, `recipes.php`) définit son propre prefix et contient les routes correspondantes.
- Les fichiers `routes.php` et `api.php` sont des index : ils ne contiennent pas de définition de routes directement.
- `RoutePatterns` (`app/Utils/RoutePatterns.php`) est appelé dans `routes/api.php` via `Route::patterns()` pour valider les paramètres UUID dans les routes au niveau global.
- Les routes ciblant une ressource spécifique (`{recipe}`, `{ingredient}`) sont regroupées dans un `Route::prefix('{resource}')->group(...)` séparé des routes de collection.

## Structuration du dossier tests

- Les tests sont organisés en deux catégories : `Unit/` et `Feature/`.
- Chaque catégorie a sa propre base class : `TestUnitCase` et `TestFeatureCase`.
- La structure interne des dossiers de tests reflète la structure des domaines métier :

```
tests/
├── TestCase.php
├── Feature/
│   ├── TestFeatureCase.php
│   └── Domain/
│       ├── Auth/
│       ├── Recipe/
│       └── Ingredient/
└── Unit/
    ├── TestUnitCase.php
    └── Domain/
        ├── Auth/
        ├── Recipe/
        └── Ingredient/
```
