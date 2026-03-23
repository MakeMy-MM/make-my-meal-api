# Carte des Domaines

- **Auth** : Gestion de l'authentification (inscription, connexion, tokens).
- **User** : Gestion du profil utilisateur. Aggregate root : `User`.
- **Ingredient** : Gestion des ingrédients disponibles pour la composition de recettes. Aggregate root : `Ingredient`.
- **Recipe** : Gestion des recettes, de leurs ingrédients et de leurs étapes de préparation. Aggregate root : `Recipe` — entités enfants : `RecipeIngredient`, `RecipeStep`.
