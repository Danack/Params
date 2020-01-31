FROM php:7.2-cli
RUN apt-get update
RUN apt-get install -y git zip unzip
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Make the default directory you
WORKDIR /var/app


ADD entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]