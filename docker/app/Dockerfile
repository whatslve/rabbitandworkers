# -----------------------------
# PHP 8.4 FPM + Laravel 12 + sockets + composer
# -----------------------------
FROM php:8.4-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zlib1g-dev nginx \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем PHP расширения
# pdo, pdo_pgsql, zip, sockets
RUN docker-php-ext-install pdo pdo_pgsql zip sockets

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Создаём папку для PHP-FPM runtime
RUN mkdir -p /var/run/php

# Копируем кастомный php.ini для включения sockets (если нужно)
# В PHP 8.4 обычно встроено, так что можно закомментировать
# COPY docker/app/php-sockets.ini /usr/local/etc/php/conf.d/docker-php-sockets.ini

# CMD для запуска PHP-FPM
CMD ["php-fpm"]
