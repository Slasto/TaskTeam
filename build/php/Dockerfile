FROM php:apache

WORKDIR /var/www/html/

# Install dependencies for composer
RUN apt-get update && apt-get install -y \
libzip-dev zip unzip \
libonig-dev \
libxml2-dev \
libpng-dev \
libjpeg-dev \
exif \
npm \
--fix-missing

# ---Abilita PDO---
RUN docker-php-ext-install pdo pdo_mysql

# ---Installazione TailwindCSS---
RUN npm install -D tailwindcss @tailwindcss/forms 
# RUN npx tailwindcss -i src/public/css/input.css -o src/public/css/output.css

# ---Importa nuova configurazione su apache2---
COPY swbd_Project.conf /etc/apache2/sites-available/
RUN a2enmod rewrite
RUN a2dissite 000-default
RUN a2ensite swbd_Project.conf

RUN service apache2 restart