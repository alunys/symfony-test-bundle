FROM php:7.2.1-fpm-alpine3.7

COPY docker/install-composer.sh /usr/local/bin/install-composer
COPY docker/entrypoint.sh /usr/local/bin/entrypoint

RUN chmod +x /usr/local/bin/install-composer \
    && install-composer \
    && mkdir /srv/symfony-test-bundle

WORKDIR /srv/symfony-test-bundle

COPY composer.json ./

RUN chmod +x /usr/local/bin/entrypoint

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]