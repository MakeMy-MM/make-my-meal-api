COMPOSE := docker compose --env-file .env -f docker/dev/compose.yaml
APP := $(COMPOSE) exec -e XDEBUG_MODE=off app

# Docker
up:
	XDEBUG_MODE=debug $(COMPOSE) up -d

up-build:
	XDEBUG_MODE=debug $(COMPOSE) up -d --build

down:
	$(COMPOSE) down

restart:
	$(COMPOSE) down
	$(COMPOSE) up -d

logs:
	$(COMPOSE) logs -f app

bash:
	$(APP) sh

# Composer
install:
	$(APP) composer install

# Database
migrate:
	$(APP) php artisan migrate

migrate-rollback:
	$(APP) php artisan migrate:rollback --step=1

migrate-refresh:
	$(APP) php artisan migrate:refresh

seed:
	$(APP) php artisan db:seed

fresh:
	$(APP) php artisan migrate:fresh --seed

# Artisan
artisan:
	$(APP) php artisan $(cmd)

# Tests
test:
	$(APP) php artisan test

test-debug:
	$(COMPOSE) exec -e XDEBUG_MODE=debug app php artisan test

# Code quality
format:
	$(APP) ./vendor/bin/php-cs-fixer fix

format-fix:
	$(APP) ./vendor/bin/php-cs-fixer fix --dry-run --diff

analyse:
	$(APP) ./vendor/bin/phpstan analyse --memory-limit=2G

# Hooks
hooks:
	git config core.hooksPath hooks
