FROM php:7.2.14-apache

RUN adduser -u 1000 --disabled-password --gecos "" appuser
RUN mkdir /home/appuser/.ssh
RUN chown -R appuser:appuser /home/appuser/
RUN echo "StrictHostKeyChecking no" >> /home/appuser/.ssh/config
RUN echo "export COLUMNS=300" >> /home/appuser/.bashrc

RUN set -x \
    && apt-get update -y && apt-get upgrade -y \
    && apt-get install -y libldap2-dev git ghostscript libxrender1 \
    && apt-get install -y libpng-dev zlib1g-dev libxml2-dev libicu-dev libzip-dev \
    && apt-get install -y libfreetype6-dev libjpeg62-turbo-dev \
    && apt-get install -y fontconfig libfontconfig xvfb wget xfonts-75dpi \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ \
    && docker-php-ext-install ldap pdo_mysql zip gd \
    && apt-get purge -y --auto-remove libldap2-dev

RUN sed -i s/www-data/appuser/g /etc/apache2/envvars

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite
RUN sed -i '/Global configuration/a \

ServerName localhost \
' /etc/apache2/apache2.conf
EXPOSE 80
RUN rm -f /run/apache2/apache2.pid
CMD apachectl  -DFOREGROUND -e info

RUN mkdir Notificaciones


RUN chown -R appuser:appuser /var/www/html

WORKDIR /var/www/html