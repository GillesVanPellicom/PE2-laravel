#!/bin/bash

# Exit immediately if a command fails
set -e


# Run Composer install inside the container
./vendor/bin/sail composer install

# Fetch Node modules inside the container
./vendor/bin/sail npm install

# Run database migrations and seed
./vendor/bin/sail php artisan migrate:fresh --seed

./vendor/bin/sail php artisan config:clear
./vendor/bin/sail php artisan cache:clear
./vendor/bin/sail php artisan route:clear
./vendor/bin/sail php artisan view:clear

git add .

./vendor/bin/sail stop
./vendor/bin/sail up -d


