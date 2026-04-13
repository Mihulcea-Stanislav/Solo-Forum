FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Fix MPM conflict
RUN a2dismod mpm_event || true && \
    a2dismod mpm_worker || true && \
    a2enmod mpm_prefork

COPY . /var/www/html/

RUN a2enmod rewrite
RUN chown -R www-data:www-data /var/www/html

CMD sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf && apache2-foreground