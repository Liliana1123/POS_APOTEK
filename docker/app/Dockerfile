# -----------------------------------------------------------------------------
# Stage 1: Build frontend assets (Native Host Platform)
# -----------------------------------------------------------------------------
FROM --platform=$BUILDPLATFORM node:22-alpine AS nodebuild
WORKDIR /app
COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
RUN NODE_OPTIONS="--max-old-space-size=3072" npm run build

# -----------------------------------------------------------------------------
# Stage 2: Vendor builder (Runs on host architecture, fast & no QEMU network overhead)
# -----------------------------------------------------------------------------
FROM --platform=$BUILDPLATFORM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_PROCESS_TIMEOUT=1800 \
    COMPOSER_IPRESOLVE=4
RUN --mount=type=cache,target=/root/.composer/cache \
    php -d default_socket_timeout=1800 /usr/bin/composer install \
    --no-interaction --prefer-dist --no-progress \
    --no-scripts --no-autoloader --no-dev --ignore-platform-reqs

# -----------------------------------------------------------------------------
# Stage 3: Production Application (Target Platform: linux/amd64)
# -----------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS app

WORKDIR /var/www/html

# 1. Install system dependencies, PHP extensions, Redis, and Nginx in one layer
RUN apk update && apk add --no-cache \
    nginx \
    zip \
    unzip \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    busybox-suid \
    supervisor \
    mariadb-client \
    && apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql intl gd zip opcache bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear \
    && rm -rf /etc/nginx/http.d/default.conf \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# 1.5 Setup crontab for Laravel Scheduler
RUN echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1" > /etc/crontabs/root

# 2. Copy vendor dependencies from native vendor builder stage
COPY composer.json composer.lock ./
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 3. Copy application source code
COPY . /var/www/html

# 3.5 Generate autoloader after files copied & remove composer
RUN composer dump-autoload --optimize --no-scripts \
    && rm -f /usr/bin/composer

# 4. Copy configurations and built assets
COPY .env.docker /var/www/html/.env
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/app/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/app/entrypoint.sh /usr/local/bin/app-entrypoint.sh
COPY docker/app/supervisord.conf /etc/supervisord.conf
COPY --from=nodebuild /app/public/build /var/www/html/public/build

# 5. Run Artisan setup, caching, and fix permissions in a unified block
RUN rm -f bootstrap/cache/*.php \
    && mkdir -p storage/app/public storage/app/private storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs \
    && php artisan key:generate --force || true \
    && php artisan package:discover || true \
    && php artisan storage:link --force || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true \
    && rm -rf /var/www/html/storage/logs/* \
    && chmod +x /usr/local/bin/app-entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public \
    && chmod -R 755 /var/www/html/storage /var/www/html/public

EXPOSE 80 9000

ENTRYPOINT ["/usr/local/bin/app-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
