#!/bin/bash

# Exit immediately if a command fails
set -e

# Define the container name (default Laravel Sail service name)
CONTAINER_NAME="pe2-laravel-laravel.test-1"

# Run Composer install inside the container
docker exec -it "$CONTAINER_NAME" composer install

# Fetch Node modules inside the container
docker exec -it "$CONTAINER_NAME" npm install

# Run database migrations and seed
docker exec -it "$CONTAINER_NAME" php artisan migrate:fresh --seed

npm run build

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan queue:restart