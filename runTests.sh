#!/usr/bin/env bash


set -e
set -x

php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib

php vendor/bin/phpunit -c test/phpunit.xml

php phpstan.phar analyze -c ./phpstan.neon -l 4 lib

php vendor/bin/infection --log-verbosity=2

php 1_errors_as_exception.php > /dev/null
php 2_errors_returned.php > /dev/null
php 3_magic.php > /dev/null