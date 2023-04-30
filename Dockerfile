FROM php:8.1-fpm

RUN apt-get update && \
    apt-get install -y libpng-dev libonig-dev libzip-dev libjpeg62-turbo-dev libc-client-dev libkrb5-dev libbz2-dev

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure imap --with-kerberos --with-imap-ssl
RUN docker-php-ext-install imap
RUN docker-php-ext-install bz2
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl 
RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install gd

WORKDIR /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

EXPOSE 9000

CMD ["php-fpm"]
