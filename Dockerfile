FROM php:8.2-apache

# Install MySQL extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your project into Apache folder
COPY . /var/www/html/

# Enable Apache rewrite (important for many PHP apps)
RUN a2enmod rewrite

# Fix permissions
RUN chown -R www-data:www-data /var/www/html

# Railway uses dynamic PORT → we must adapt Apache
CMD sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf && apache2-foreground