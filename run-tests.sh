#!/bin/bash

# Log 
function log_output() {
    echo $(date) :: Territory Services Api - Tests :: $1
}

# Refresh database
function refresh_db() {
    rm database/database.sqlite
    touch database/database.sqlite
    php artisan migrate:refresh --seed --database=sqlite --env=testing
    log_output "DB refreshed"
}

log_output "Refresh DB"; 
refresh_db

php artisan test --env=testing