#!/bin/sh
mv .env .env-old
cp .env-docker .env

# install composer dependencies
docker run --rm -v $(pwd):/app composer install --no-dev --ignore-platform-req=ext-gd

# load variables from .env
source .env
# check if sqlite or mysql and start docker with the appropriate yml
if [[ $DB_CONNECTION == "sqlite" ]]; then
  docker compose -f docker-compose-sqlite.yml up -d 
else
  docker compose up -d
fi

sleep 20

# prepare laravel
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:refresh --seed
docker compose exec app php artisan config:cache
