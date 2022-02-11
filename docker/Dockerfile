FROM php:7.4.15-fpm-alpine

RUN apk update && apk upgrade
RUN apk add bash

RUN docker-php-ext-install bcmath

ADD php.ini /usr/local/etc/php/php.ini

RUN apk add composer

WORKDIR /var/www/double-entry
