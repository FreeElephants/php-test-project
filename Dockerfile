FROM php:7.4-cli

# Composer requirements begin
RUN apt-get update \
    && apt-get install -y \
    libzip-dev \
    unzip

RUN docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Composer requirements end

# Xdebug begin
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Xdebug end

WORKDIR /var/www
