ARG php_version

FROM 3liz/liz-php-fpm:${php_version}


ARG php_version
ARG DEBIAN_FRONTEND=noninteractive
ENV PHP_VERSION=${php_version}

RUN sed -i "s/^error_reporting =.*/error_reporting = E_ALL\\|E_DEPRECATED\\|E_STRICT/g" /etc/php/${PHP_VERSION}/cli/php.ini

RUN apt-get update; \
    apt-get -y install \
    php${PHP_VERSION}-soap \
    ; apt-get clean

COPY appctl.sh /bin/

RUN chmod 755 /bin/appctl.sh

WORKDIR /app/tests

