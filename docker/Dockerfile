FROM ubuntu:24.04

ENV DEBIAN_FRONTEND=noninteractive

# Fix timezone issue
ENV TZ=Europe/London
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone


RUN apt-get update && apt-get dist-upgrade -y


# Install the relevant packages
# Need mysql-client for mysqldump command on backup endpoint.
RUN apt-get install -y \
    vim cron curl apache2 libapache2-mod-php8.3 \
    php8.3-curl

# Install composer 2
RUN apt-get update \
  && apt-get install curl -y \
  && curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/bin/composer \
  && chmod +x /usr/bin/composer
  
# Install node for mjml
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - \
  && apt-get install -y nodejs -y
  


# Enable the apache mods
RUN a2enmod php8.3 && a2enmod rewrite


# expose port 80 and 443 (ssl) for the web requests
EXPOSE 80


# Manually set the apache environment variables in order to get apache to work immediately.
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
ENV APACHE_LOG_DIR=/var/log/apache2

# It appears that the new apache requires these env vars as well
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_PID_FILE=/var/run/apache2/apache2.pid


###### Update the php INI settings #########

# Uncomment any of these if you need them.

# Increase php's max allowed memory size
RUN sed -i 's;memory_limit = .*;memory_limit = -1;' /etc/php/8.3/apache2/php.ini
RUN sed -i 's;memory_limit = .*;memory_limit = -1;' /etc/php/8.3/cli/php.ini

RUN sed -i 's;display_errors = .*;display_errors = On;' /etc/php/8.3/apache2/php.ini
RUN sed -i 's;display_errors = .*;display_errors = On;' /etc/php/8.3/cli/php.ini




# Add the site's code to the container.
# We could mount it with volume, but by having it in the container, deployment is easier.
COPY --chown=root:www-data ../site /var/www/site


# Install npm packages.
RUN cd /var/www/site && npm install


# Update our apache sites available with the config we created
ADD docker/apache-config.conf /etc/apache2/sites-enabled/000-default.conf


# Set permissions
#RUN chown root:www-data --recursive /var/www
RUN cd /var/www/site && composer install && chown root:www-data --recursive vendor

RUN chown root:www-data /var/www
RUN chmod 750 --recursive /var/www

# Execute the containers startup script which will start many processes/services
# The startup file was already added when we added "project"
COPY --chown=root:root docker/startup.sh /root/startup.sh
CMD ["/bin/bash", "/root/startup.sh"]
