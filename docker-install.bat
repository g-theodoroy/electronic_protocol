ren .env .env-old
copy .env-docker .env
set "pwd=%cd%"
docker run --rm -v %pwd%:/app composer install --no-dev --ignore-platform-req=ext-gd
docker compose up -d
timeout /t 20
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:refresh --seed
docker compose exec app php artisan config:cache
