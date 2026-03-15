web: php artisan serve --host=0.0.0.0 --port=$PORT
worker: sh -c "while true; do php artisan schedule:run; sleep 60; done"