FROM php:apache
WORKDIR /var/www/html/

RUN apt-get update && apt-get install -y npm --fix-missing
RUN docker-php-ext-install pdo pdo_mysql

RUN npm install -D tailwindcss @tailwindcss/forms
# if you need to build css do:
# npx tailwindcss -i /var/www/html/src/public/css/input.css -o /var/www/html/src/public/css/outsput.css

COPY swbd_Project.conf /etc/apache2/sites-available/
RUN a2enmod rewrite
RUN a2ensite swbd_Project.conf
RUN service apache2 restart