FROM php:apache

# Install dependencies for composer
RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        libonig-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev && \
        docker-php-ext-configure gd --with-jpeg && \
        docker-php-ext-install \
        pdo_mysql \
        zip \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gdt 

        #npm

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php — install-dir=/usr/local/bin — filename=composer \
    && php -r "unlink('composer-setup.php');"

RUN apt-get install npm

# --Installazione Tailwind---
# npm install -D tailwindcss

# - Compilazione CSS -
# npx tailwindcss -i ./src/input.css -o ./src/output.css --watch

# ---Importa nuova configurazione su apache2---
# a2enmod rewrite 
# a2ensite /etc/apache2/sites-available/swbd_Project.conf
# service apache2 reload

# ---Abbilitiamo PDO_MySql---
# troviamo la posizione di php.ini-* con: `php --ini`
# cd /usr/local/etc/php
# modifichiamo i file `php.ini-production` e `php.ini-development` e decommentiamo la stringa `extension=pdo_mysql` 
# docker-php-ext-install pdo pdo_mysql
# service apache2 reload