#!/bin/sh
set -e

cd /var/www/html

# Ensure .env exists
if [ ! -f ".env" ]; then
  cp .env.docker .env
fi

# Generate app key if missing
if ! grep -qE '^APP_KEY=.+$' .env; then
  php artisan key:generate --force || true
fi

# Ensure required cache directories, storage subdirectories, and storage link exist
mkdir -p storage/app/public/info-apotek \
         storage/logs \
         storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         bootstrap/cache || true

rm -rf public/storage || true
php artisan storage:link --force || true

chown -R www-data:www-data storage bootstrap/cache public || true
chmod -R 755 storage bootstrap/cache public || true

# Attempt DB migrations when DB is reachable (Optional if RUN_MIGRATIONS=true)
if [ "${RUN_MIGRATIONS}" = "true" ] && [ "${DB_HOST}" != "" ]; then
  echo "Waiting for database ${DB_HOST}:${DB_PORT:-3306} for migrations..."
  tries=0
  max_tries=60
  DB_PORT="${DB_PORT:-3306}"
  until php -r "try{new PDO('mysql:host=${DB_HOST};port=${DB_PORT}', getenv('DB_USERNAME'), getenv('DB_PASSWORD'));}catch(Exception \$e){exit(1);}"; do
    tries=$((tries+1))
    if [ "$tries" -ge "$max_tries" ]; then
      echo "Database not ready, skipping migrations."
      break
    fi
    sleep 2
  done

  if [ "$tries" -lt "$max_tries" ]; then
    echo "Database ready. Running migrations..."
    php artisan migrate --force || true
    if [ "${RUN_SEEDERS}" = "true" ]; then
      echo "Running seeders..."
      php artisan db:seed --force || true
    fi
  fi
fi

# Start Nginx web server and crond only when running as main app (supervisord)
if [ "$1" != "php" ] && [ "$1" != "artisan" ]; then
  nginx -g "daemon on;"
  crond -b -l 8
fi

exec "$@"
