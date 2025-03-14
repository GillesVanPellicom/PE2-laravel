#!/bin/bash

# Exit immediately if a command fails
set -e

# Define the container name (default Laravel Sail service name)
CONTAINER_NAME="pe2-laravel-laravel.test-1"

# Run Composer install inside the container
docker exec "$CONTAINER_NAME" composer install

# Fetch Node modules inside the container
docker exec "$CONTAINER_NAME" npm install

# Run database migrations and seed
docker exec "$CONTAINER_NAME" php artisan migrate:fresh --seed

docker exec "$CONTAINER_NAME" php artisan config:clear
docker exec "$CONTAINER_NAME" php artisan cache:clear
docker exec "$CONTAINER_NAME" php artisan route:clear
docker exec "$CONTAINER_NAME" php artisan view:clear

docker exec "$CONTAINER_NAME" npm run dev

