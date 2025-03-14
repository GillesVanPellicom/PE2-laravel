#!/bin/bash

# Exit immediately if a command fails
set -e

# Define the container name (default Laravel Sail service name)
CONTAINER_NAME="pe2-laravel-laravel.test-1"

# Run Composer install inside the container
./vendor/sail composer install

# Fetch Node modules inside the container
./vendor/sail npm install

# Run database migrations and seed
./vendor/sail php artisan migrate:fresh --seed

./vendor/sail php artisan config:clear
./vendor/sail php artisan cache:clear
./vendor/sail php artisan route:clear
./vendor/sail php artisan view:clear

