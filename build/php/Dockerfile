FROM php:apache
WORKDIR /var/www/html/

RUN apt-get update && apt-get upgrade -y --fix-missing
RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite
COPY swbd_Project.conf /etc/apache2/sites-available/000-default.conf

# if you need to build css do:
# RUN apt-get install npm
# RUN npm install -D tailwindcss @tailwindcss/forms
# npx tailwindcss -i /var/www/html/src/public/css/input.css -o /var/www/html/src/public/css/outsput.css

