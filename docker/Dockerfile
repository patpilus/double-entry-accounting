FROM php:8.0-fpm-alpine

RUN apk update && apk upgrade
RUN apk add bash

RUN docker-php-ext-install bcmath

ADD php.ini /usr/local/etc/php/php.ini

RUN apk add composer

WORKDIR /var/www/double-entry
