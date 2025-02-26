# Use an official PHP-FPM base image
FROM php:8.3-fpm

LABEL maintainer="Sigurd Nes <sigurdne@gmail.com>"

# Define build arguments
ARG INSTALL_MSSQL=false
ARG INSTALL_XDEBUG=false
ARG INSTALL_ORACLE=false

ARG http_proxy
ARG https_proxy

ENV http_proxy=${http_proxy}
ENV https_proxy=${https_proxy}

# Download and install the install-php-extensions script
# https://github.com/mlocati/docker-php-extension-installer
RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

# Configure PEAR
RUN if [ -n "${http_proxy}" ]; then pear config-set http_proxy ${http_proxy}; fi && \
    pear config-set php_ini $PHP_INI_DIR/php.ini


# Conditionally install Oracle support
RUN if [ "${INSTALL_ORACLE}" = "true" ]; then \
    echo "Installing Oracle support..."; \
     # Install OCI8 and PDO_OCI extensions
    install-php-extensions oci8 pdo_oci; \
else \
    echo "Skipping Oracle support installation."; \
fi

# Install necessary packages
RUN apt-get update && apt-get install -y software-properties-common \
    apt-utils libcurl4-openssl-dev libicu-dev libxslt-dev libpq-dev \
    zlib1g-dev libpng-dev libfreetype-dev libjpeg62-turbo-dev \ 
    libc-client-dev libkrb5-dev libzip-dev libonig-dev \
    git \
    less vim-tiny \
    apg \
    sudo \
    libaio1 locales wget \
    libmagickwand-dev --no-install-recommends \
    apache2 libapache2-mod-fcgid ssl-cert \
	cron \
	iputils-ping \
	wkhtmltopdf

RUN touch /etc/cron.d/cronjob && chmod 0644 /etc/cron.d/cronjob

# Generate the specified locale
RUN locale-gen --purge en_US.UTF-8

# Set environment variables
ENV LC_ALL=en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US.UTF-8


# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) curl intl xsl pdo_pgsql pgsql pdo_mysql gd \
    shmop soap zip mbstring ftp calendar exif

RUN install-php-extensions imap

# Install PECL extensions
RUN pecl install apcu && docker-php-ext-enable apcu
RUN pecl install redis && docker-php-ext-enable redis

# Add APCu configuration
RUN echo "apc.shm_size=128M" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini \
    && echo "apc.enabled=1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini \
    && echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini


# Install OPcache
RUN docker-php-ext-install opcache

# Add OPcache configuration
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Install Imagick
RUN install-php-extensions imagick

# Install Composer
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
RUN php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Conditionally install MSSQL support
RUN if [ "${INSTALL_MSSQL}" = "true" ]; then \
     install-php-extensions sqlsrv pdo_sqlsrv;\
fi

# PHP configuration
RUN if [ "${INSTALL_XDEBUG}" = "true" ]; then \
    pecl install xdebug && docker-php-ext-enable xdebug; \
    echo 'xdebug.mode=debug,develop' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.discover_client_host=1' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.start_with_request=yes' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.idekey=netbeans-xdebug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
   fi

RUN echo 'session.cookie_secure=Off' >> /usr/local/etc/php/php.ini
RUN echo 'session.use_cookies=On' >> /usr/local/etc/php/php.ini
RUN echo 'session.use_only_cookies=On' >> /usr/local/etc/php/php.ini
RUN echo 'short_open_tag=Off' >> /usr/local/etc/php/php.ini
RUN echo 'request_order = "GPCS"' >> /usr/local/etc/php/php.ini
RUN echo 'variables_order = "GPCS"' >> /usr/local/etc/php/php.ini
RUN echo 'memory_limit = 5048M' >> /usr/local/etc/php/php.ini
RUN echo 'max_input_vars = 5000' >> /usr/local/etc/php/php.ini
RUN echo 'error_reporting = E_ALL & ~E_NOTICE' >> /usr/local/etc/php/php.ini
RUN echo 'post_max_size = 20M' >> /usr/local/etc/php/php.ini
RUN echo 'upload_max_filesize = 8M' >> /usr/local/etc/php/php.ini

# Install Java
RUN wget -qO - https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/apt/trusted.gpg.d/microsoft.asc.gpg && \
    echo "deb [arch=amd64] https://packages.microsoft.com/debian/$(cat /etc/debian_version | cut -d. -f1)/prod $(lsb_release -cs) main" > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update && apt-get install -y msopenjdk-21 unzip

## Verify Java installation
RUN java -version

RUN mkdir -p /var/public/files
RUN chmod 777 /var/public/files

# Ensure PHP-FPM socket directory exists
RUN mkdir -p /run/php && chown www-data:www-data /run/php

# Update PHP-FPM configuration to use Unix socket
RUN sed -i 's|^listen = .*|listen = /run/php/php-fpm.sock|' /usr/local/etc/php-fpm.d/www.conf \
    && echo 'listen.owner = www-data' >> /usr/local/etc/php-fpm.d/www.conf \
    && echo 'listen.group = www-data' >> /usr/local/etc/php-fpm.d/www.conf \
    && echo 'listen.mode = 0660' >> /usr/local/etc/php-fpm.d/www.conf

# Alternative: Update PHP-FPM configuration to use TCP socket
#RUN sed -i 's|^listen = .*|listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf

# Update include directive in php-fpm.conf
RUN sed -i 's|^include=.*|include=/usr/local/etc/php-fpm.d/*.conf|' /usr/local/etc/php-fpm.conf

# Comment out conflicting listen directives
RUN sed -i 's|^listen = .*|;listen = 127.0.0.1:9000|' /usr/local/etc/php-fpm.d/www.conf.default
RUN sed -i 's|^listen = .*|;listen = 9000|' /usr/local/etc/php-fpm.d/zz-docker.conf

# Copy PHP-FPM configuration
#COPY php-fpm.conf /etc/apache2/conf-available/php-fpm.conf

RUN echo '<IfModule mod_proxy_fcgi.c>\n\
    <FilesMatch \.php$>\n\
        SetHandler "proxy:unix:/run/php/php-fpm.sock|fcgi://localhost"\n\
    </FilesMatch>\n\
</IfModule>' > /etc/apache2/conf-available/php-fpm.conf

# Apache2 configuration
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APP_DOCUMENT_ROOT=/var/www/html

EXPOSE 80


# Enable Apache modules
RUN a2enmod proxy_fcgi setenvif
RUN a2enconf php-fpm
RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod ssl
RUN a2enmod proxy
RUN a2enmod proxy_http

# Copy Apache configuration
#COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy entrypoint script
#COPY docker-entrypoint.sh /usr/local/bin/

RUN echo '#!/bin/sh\n\
set -e\n\
# start cron\n\
service cron start\n\
# Start PHP-FPM\n\
php-fpm &\n\
# Start Apache\n\
php-fpm &\n\
exec apache2ctl -D FOREGROUND' > /usr/local/bin/docker-entrypoint.sh


# Make entrypoint script executable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]