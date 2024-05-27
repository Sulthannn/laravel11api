FROM php:8.0-apache

RUN apt-get update && apt-get install -y libssl1.0.0 libssl-dev

COPY . /var/www/html/

CMD ["apache2-foreground"]