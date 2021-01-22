#!/bin/sh -l

set -e

# comment in to debug
# tail -f README.md

echo '---Installing dependencies---'
php ./composer.phar install

echo '---Running unit tests---'
bash runTests.sh

