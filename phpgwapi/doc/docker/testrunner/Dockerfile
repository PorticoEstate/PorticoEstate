FROM php:7.1-cli-jessie

RUN apt-get update && apt-get install -y zlibc zlib1g zlib1g-dev libpq-dev postgresql-client
RUN docker-php-ext-install -j$(nproc) zip pgsql pdo pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY run.sh /run.sh

ENTRYPOINT ["/bin/bash"]
CMD ["/run.sh"]