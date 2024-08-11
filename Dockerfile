FROM php:8.3-fpm AS builder

RUN apt-get update && apt-get install -y libpq-dev libicu-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/app

COPY . .

RUN composer install --no-dev --optimize-autoloader


FROM php:8.3-fpm

RUN apt-get update && apt-get install -y libpq-dev libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_pgsql pgsql 

COPY --from=builder /var/www/app /var/www/app

RUN chown -R www-data:www-data /var/www/app/storage

RUN chmod -R 775 /var/www/app/storage

WORKDIR /var/www/app

EXPOSE 9000

CMD ["php-fpm"]
