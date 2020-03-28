#!/usr/bin/env bash

set -e
# set -x

# sh runCodeSniffer.sh

# sh runUnitTests.sh

# php ./phpstan.phar analyze -c ./phpstan.neon -l 7 lib

# php ./psalm.phar

sh runMutationTests.sh

sh runExamples.sh

echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
# php vendor/bin/phpunit -c test/phpunit.xml
