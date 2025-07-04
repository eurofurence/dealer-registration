FROM php:8.4.10-alpine AS base
WORKDIR /app

ENV COMPOSER_MEMORY_LIMIT=-1
######################################################
# Step 1 | Install Dependencies
######################################################
# Using https://github.com/mlocati/docker-php-extension-installer#usage
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions @composer bcmath gd intl opcache pcntl pdo_mysql redis swoole zip

######################################################
# Step 2 | Copy PHP Configuration
######################################################
COPY .github/docker/php/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY .github/docker/php/php.ini $PHP_INI_DIR/conf.d/php.ini

######################################################
# Step 3 | Install Composer packages
######################################################
COPY composer.json composer.lock /app/
RUN composer install --no-dev --no-scripts --no-autoloader
######################################################
# NodeJS Stage
######################################################
FROM node:22 AS vite
WORKDIR /app
######################################################
# Step 4 | Install Node.js packages
######################################################
COPY package.json package-lock.json vite.config.js ./
RUN npm install
######################################################
# Step 5 | Perform npm build
######################################################
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
