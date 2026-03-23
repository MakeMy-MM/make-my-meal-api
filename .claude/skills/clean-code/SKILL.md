---
name: clean-code
description: Lance le formatage (php-cs-fixer) et l'analyse statique (PHPStan) via les commandes du Makefile.
user-invocable: true
---

# Skill: Clean Code

Vérifie et corrige la qualité du code via les outils configurés dans le projet.

## Procédure d'exécution

### Etape 1 : Formatage (php-cs-fixer)

1. Lancer `make format` pour corriger automatiquement le formatage.
2. Si des fichiers ont été modifiés par le formateur, les lister à l'utilisateur.

### Etape 2 : Analyse statique (PHPStan)

1. Lancer `make analyse` pour vérifier l'analyse statique (niveau 8).
2. Si des erreurs sont détectées :
   - Lire et analyser chaque erreur.
   - Corriger les erreurs dans le code source.
   - Relancer `make analyse` pour vérifier que les corrections sont valides.
   - Répéter jusqu'à 0 erreur.

### Etape 3 : Vérification finale

1. Lancer `make format-fix` pour s'assurer que le formatage est conforme (dry-run).
2. Lancer `make analyse` une dernière fois pour confirmer 0 erreur.
3. Si les deux commandes passent sans erreur, le code est propre.

### Etape 4 : Revue des fichiers modifiés

1. Lancer `git status` pour lister tous les fichiers modifiés, ajoutés ou supprimés.
2. Lancer `git diff` pour vérifier le contenu des modifications.
3. S'assurer que seuls les fichiers attendus ont été modifiés — aucun fichier non lié ne doit être impacté.
4. Si des modifications inattendues sont détectées, en informer l'utilisateur avant de continuer.

## Règles

- Ne jamais ignorer une erreur PHPStan — toutes doivent être corrigées.
- Ne jamais ajouter de `@phpstan-ignore` ou `@phpstan-ignore-next-line` sans l'accord explicite de l'utilisateur.
- Si une erreur PHPStan semble être un faux positif, demander à l'utilisateur avant de la supprimer.
- Les corrections ne doivent pas modifier le comportement fonctionnel du code.
- Si une correction nécessite un changement architectural, en informer l'utilisateur avant de procéder.
