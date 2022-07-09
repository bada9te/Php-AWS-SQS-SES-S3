FROM php:7.4-apache

WORKDIR /var/www/html

COPY . /var/www/html

RUN useradd -ms /bin/bash admin

RUN chown -R admin:admin /var/www/html

RUN chmod 777 /var/www/html

USER admin

EXPOSE 80