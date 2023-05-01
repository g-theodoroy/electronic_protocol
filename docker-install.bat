@echo off

REM Prepare .env
ren .env .env-old
copy .env-docker .env
set "pwd=%cd%"

REM Install composer dependencies
docker run --rm -v %pwd%:/app composer install --no-dev --ignore-platform-req=ext-gd

REM Read .env variables
for /f "usebackq tokens=1* delims==" %%a in (.env) do (
  set "%%a=%%b"
)

REM Check if the database connection is SQLite
if "%DB_CONNECTION%"=="sqlite" (
  docker compose -f docker-compose-sqlite.yml up -d 
) else (
  docker compose up -d
)


timeout /t 20

REM Prepare Laravel
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate:refresh --seed
docker compose exec app php artisan config:cache
