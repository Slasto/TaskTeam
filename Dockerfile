FROM php:apache

# Install dependencies
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

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php — install-dir=/usr/local/bin — filename=composer \
    && php -r "unlink('composer-setup.php');"


# REMIND!
# a2enmod rewrite 
# a2ensite /etc/apache2/sites-available/swbd_Project.conf
# service apache2 reload

# into the laravel project folder -> chown -R www-data:www-data *
#                                 -> chmod -Rf 0777 storage; non è good practice ma almeno funge