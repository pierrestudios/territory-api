#!/bin/bash

# Log 
function log_output() {
    echo $(date) :: Territory Services Api - Tests :: $1
}

# Refresh database
function refresh_db() {
    rm database/database.sqlite
    touch database/database.sqlite
    php artisan cache:clear
    php artisan config:clear
    php artisan config:cache --env=testing
    php artisan migrate:refresh --seed --database=sqlite --env=testing --no-interaction
    log_output "DB refreshed"
}

log_output "Refresh DB"; 
refresh_db

php artisan test --env=testing

php artisan cache:clear
php artisan config:clear