FROM php:7.2-fpm

RUN apt update

RUN docker-php-ext-install mysqli &&\
    docker-php-ext-install pdo pdo_mysql
