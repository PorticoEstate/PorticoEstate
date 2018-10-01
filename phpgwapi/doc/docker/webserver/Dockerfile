FROM ubuntu:18.04

RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    apache2 \
    libapache2-mod-php \
    php \
    php-curl \
    php-intl \
    php-xdebug \
    php-apcu \
    php-dev \
    php-xmlrpc \
    php-xsl \
    php-pgsql\
    php-json \
    php-gd \
    php-imagick \
    php-imap \
    php-soap \
    php-zip \
    php-mbstring \
    php-pear \
    libaio1 \
    locales

RUN sed -i 's/"GP"/"GPCS"/' /etc/php/7.2/apache2/php.ini && \
    sed -i 's/"GP"/"GPCS"/' /etc/php/7.2/cli/php.ini

COPY custom-apache.conf /etc/php/7.2/apache2/conf.d/91-app.ini
RUN ln -s /etc/php/7.2/apache2/conf.d/91-app.ini /etc/php/7.2/cli/conf.d/91-app.ini

 # Enable mod_rewrite
RUN a2enmod rewrite

RUN mkdir -p /var/www/html/portico /var/www/portico-empty-start-data \
    && chown www-data /var/www/portico-empty-start-data

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
#ENV APACHE_LOG_DIR /var/log/apache2


EXPOSE 80

ENTRYPOINT ["apachectl"]
CMD ["-D FOREGROUND"]