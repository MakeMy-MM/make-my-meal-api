# Test Command

## Description

La commande `test:command` (`app/Console/Commands/TestCommand.php`) est utilisée pour tester des features durant le développement.

## Règles

- La **signature** (`test:command`) et la **description** ne doivent jamais être modifiées.
- Seul le contenu de la méthode `handle()` peut être modifié pour tester des fonctionnalités.
- Lancement : `make artisan cmd="test:command"`.
