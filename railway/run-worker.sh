#!/bin/bash
# Queue worker for Railway
# Railpack handles the main app, but queue workers need to be separate services
php artisan queue:work

