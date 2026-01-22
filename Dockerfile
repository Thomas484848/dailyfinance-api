FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git unzip libpq-dev \
  && docker-php-ext-install pdo pdo_pgsql \
  && a2enmod rewrite headers \
  && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
   && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf \
   && printf '%s\n' \
      '<Directory /var/www/html/public>' \
      '  AllowOverride All' \
      '  Require all granted' \
      '</Directory>' \
      > /etc/apache2/conf-available/symfony.conf \
   && a2enconf symfony \
   && echo 'ServerName localhost' > /etc/apache2/conf-available/servername.conf \
   && a2enconf servername

RUN composer install --no-dev --optimize-autoloader --no-scripts \
 && mkdir -p var \
 && chown -R www-data:www-data var

EXPOSE 80
