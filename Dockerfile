FROM php:8.1-fpm

# Установка необходимых пакетов
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip \
    bcmath

# Установка Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Копирование исходного кода
COPY . /var/www/html/

# Установка зависимостей через Composer
RUN composer install --no-dev --optimize-autoloader