FROM php:apache

# Install dependencies for composer
RUN apt-get update && apt-get install npm -y

RUN apt-get install npm

# --Installazione Tailwind---
RUN npm install -D tailwindcss @tailwindcss/forms

# ---Importa nuova configurazione su apache2---
RUN a2enmod rewrite 
RUN a2ensite /etc/apache2/sites-available/swbd_Project.conf
RUN service apache2 reload

# ---Abbilitiamo PDO_MySql---
# troviamo la posizione di php.ini-* con: `php --ini`
# cd /usr/local/etc/php
# modifichiamo i file `php.ini-production` e `php.ini-development` e decommentiamo la stringa `extension=pdo_mysql` 
RUN /usr/local/etc/php/docker-php-ext-install pdo pdo_mysql

# - Compilazione CSS -
# npx tailwindcss -i ./src/public/css/input.css -o ./src/public/css/output.css --watch
RUN npx tailwindcss -i ./src/public/css/input.css -o ./src/public/css/output.css

RUN service apache2 reload