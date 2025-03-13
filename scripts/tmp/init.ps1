# Exit immediately if a command fails
$ErrorActionPreference = "Stop"

# Define the container name (default Laravel Sail service name)
$CONTAINER_NAME = "pe-laravel-laravel.test-1"

# Run Composer install inside the container
docker exec -it $CONTAINER_NAME composer install

# Fetch Node modules inside the container
docker exec -it $CONTAINER_NAME npm install

# Run database migrations and seed
docker exec -it $CONTAINER_NAME php artisan migrate:fresh --seed