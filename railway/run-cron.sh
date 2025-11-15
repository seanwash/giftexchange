#!/bin/bash
# Laravel scheduler for Railway
# Railpack handles the main app, but schedulers need to be separate services
while [ true ]; do
    php artisan schedule:run --verbose --no-interaction
    sleep 60
done

