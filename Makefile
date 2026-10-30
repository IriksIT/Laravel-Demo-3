# Alle commando's draaien via Docker; lokaal zijn PHP en Composer niet nodig.
COMPOSER = docker run --rm -it -u $$(id -u):$$(id -g) -e HOME=/tmp -e COMPOSER_HOME=/tmp/composer -v $(CURDIR)/app:/app -w /app composer:2
PHP      = docker compose run --rm app php

.PHONY: install up down logs fresh test artisan shell

## Eerste keer: dependencies installeren, .env aanmaken, database migreren en vullen met demo-data
install:
	$(COMPOSER) composer install --no-interaction --prefer-dist
	@test -f app/.env || cp app/.env.example app/.env
	@touch app/database/database.sqlite
	$(PHP) artisan key:generate --ansi
	$(PHP) artisan migrate:fresh --seed --force

## Start de applicatie (zie docker-compose.yml voor de poort)
up:
	docker compose up -d
	@echo "Applicatie draait op http://localhost:$$(docker compose port app 8000 | sed 's/.*://')"

down:
	docker compose down

logs:
	docker compose logs -f app

## Database opnieuw opbouwen met verse demo-data
fresh:
	$(PHP) artisan migrate:fresh --seed --force

test:
	$(PHP) artisan test

## Willekeurig artisan-commando: make artisan cmd="route:list"
artisan:
	$(PHP) artisan $(cmd)

shell:
	docker compose run --rm app sh
