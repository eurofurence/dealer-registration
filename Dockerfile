FROM php:8.3-alpine AS base
WORKDIR /app

ENV COMPOSER_MEMORY_LIMIT=-1
######################################################
# Step 1 | Install Dependencies
######################################################
COPY .github/docker/install-php-extensions /usr/local/bin/

RUN apk update \
    && chmod +x /usr/local/bin/install-php-extensions \
    && apk add --no-cache curl git unzip openssl tar ca-certificates \
    && install-php-extensions gd bcmath pdo_mysql zip intl opcache pcntl redis swoole @composer \
    && rm -rf /var/cache/apk/*

######################################################
# Copy Configuration
######################################################
COPY .github/docker/php/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY .github/docker/php/php.ini $PHP_INI_DIR/conf.d/php.ini

######################################################
# Step 6 | Configure Credentials & Hosts for external Git (optional)
######################################################
COPY composer.json composer.lock /app/
RUN composer install --no-dev --no-scripts --no-autoloader
######################################################
# Local Stage
######################################################
FROM base AS local
######################################################
# NodeJS Stage
######################################################
FROM node:22 AS vite
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm install
COPY ./resources /app/resources
RUN npm run build
######################################################
# Production Stage
######################################################
FROM base AS production
COPY --from=vite /app/public/build ./public/build
COPY . /app/
RUN composer install --no-dev --optimize-autoloader \
    && chmod 777 -R bootstrap storage \
    && rm -rf .env bootstrap/cache/*.php auth.json \
    && chown -R www-data:www-data /app \
    && rm -rf ~/.composer
CMD [ "sh", "-c", "php artisan octane:start --host=0.0.0.0 --port=80" ]
