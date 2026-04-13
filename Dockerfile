FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# 🔥 FULL RESET OF MPM (critical fix)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_prefork.load

# Enable ONLY prefork
RUN a2enmod mpm_prefork

# Enable required modules
RUN a2enmod rewrite

# Copy project
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

# Railway port fix
CMD sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf && apache2-foreground