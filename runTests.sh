#!/usr/bin/env bash

set -e
set -x

php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib

php vendor/bin/phpunit -c test/phpunit.xml

php phpstan.phar analyze -c ./phpstan.neon -l 4 lib

set +e

php vendor/bin/infection --log-verbosity=2 --only-covered --min-covered-msi=100

infection_exit_code=$?

set -e

if [ "$infection_exit_code" -ne "0" ]; then echo "Infection failed"; cat infection-log.txt;  exit "$infection_exit_code"; fi

php lib/ParamsExample/1_errors_as_exception.php
php lib/ParamsExample/2_errors_returned.php
php lib/ParamsExample/3_magic.php


