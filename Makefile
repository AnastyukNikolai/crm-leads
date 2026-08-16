.PHONY: build up down restart shell install migrate seed fresh test pint logs

export COMPOSE_PROJECT_NAME := crm-leads

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down

restart: down up

shell:
	docker compose exec php bash

install:
	docker compose exec php composer install

migrate:
	docker compose exec php php artisan migrate

seed:
	docker compose exec php php artisan db:seed

fresh:
	docker compose exec php php artisan migrate:fresh --seed

test:
	docker compose exec php php artisan test

pint:
	docker compose exec php vendor/bin/pint

logs:
	docker compose logs -f
