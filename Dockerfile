FROM php:8.2-apache

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Enable Apache rewrite if needed
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy app files into container
COPY . /var/www/html

# Ensure permissions are reasonable
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
