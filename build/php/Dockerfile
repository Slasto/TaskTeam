FROM php:apache

# Install dependencies for composer
RUN apt-get update && apt-get install -y \
        libzip-dev \
        zip \
        unzip \
        libonig-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
        exif \
        && docker-php-ext-install pdo pdo_mysql gd

# ---Importa nuova configurazione su apache2---
RUN a2enmod rewrite
COPY swbd_Project.conf /etc/apache2/sites-available/
RUN a2ensite swbd_Project.conf

# ---Compilazione CSS---
# npx tailwindcss -i ./src/public/css/input.css -o ./src/public/css/output.css --watch
# RUN npm install -D tailwindcss @tailwindcss/forms chart.js
# RUN npx tailwindcss -i ./src/public/css/input.css -o ./src/public/css/output.css
RUN service apache2 restart