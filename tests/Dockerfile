FROM php:7.2-fpm
RUN apt update
RUN apt install -y libpng-dev libgd-dev libjpeg62-turbo-dev --no-install-recommends
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ &&\
    docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo \
        pdo_mysql