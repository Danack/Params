#!/usr/bin/env bash

set -e


php vendor/bin/phpcs --standard=./test/codesniffer_tests.xml --encoding=utf-8 --extensions=php -p -s test

php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib