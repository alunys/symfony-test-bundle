#!/bin/sh

composer install --prefer-dist --no-progress --no-suggest --no-interaction

exec docker-php-entrypoint "$@"