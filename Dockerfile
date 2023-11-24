# Use an official PHP image with Apache
FROM php:7.4-apache

# Install necessary dependencies
RUN apt-get update && \
    apt-get install -y libldap2-dev zlib1g-dev && \
    rm -rf /var/lib/apt/lists/*

# Enable LDAP extension
RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap

# Enable Apache modules
RUN a2enmod rewrite

# Copy your PHP code into the container
COPY ./data /var/www/html/

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground when the container runs
CMD ["apache2-foreground"]

