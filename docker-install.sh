#!/bin/sh
mv .env .env-old
cp .env-docker .env
docker run --rm -v $(pwd):/app composer install --no-dev --ignore-platform-req=ext-gd
docker compose up -d
sleep 20
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:refresh --seed
docker compose exec app php artisan config:cache
