SHELL := /bin/bash

up:
	docker compose up -d --build

down:
	docker compose down

reset:
	docker compose down -v
	rm -rf vendor node_modules bootstrap/cache/*.php public/storage
	rm -f .env


sh:
	docker compose exec -u www-data app bash

logs:
	docker compose logs -f --tail=100

install:
	# Crea Laravel solo si no existe (no pisa nada)
	if [ ! -f artisan ]; then \
		docker compose run --rm app bash -lc 'set -e; \
		  composer create-project laravel/laravel /tmp/laravel; \
		  shopt -s dotglob; \
		  cp -an /tmp/laravel/* /var/www/html/'; \
	fi
	cp -n .env.example .env || true
	docker compose run --rm app php artisan key:generate
	docker compose run --rm app php artisan storage:link

migrate:
	docker compose run --rm app php artisan migrate

test:
	docker compose run --rm app php artisan test -q

artisan:
	@docker compose run --rm app php artisan $(CMD)
	@true
