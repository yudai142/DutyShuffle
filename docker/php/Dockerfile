FROM php:7.2-apache
RUN apt-get update && apt-get install -y libpq-dev \
  && docker-php-ext-install pdo_pgsql mbstring \
  && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

EXPOSE 80