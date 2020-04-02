#!/usr/bin/env bash

set -e
# set -x

bash runCodeSniffer.sh

bash runUnitTests.sh --no-coverage

echo "Running PHPStan"
php ./phpstan.phar analyze -c ./phpstan.neon -l 7 lib

echo "Running Psalm"
php ./psalm.phar

bash runMutationTests.sh

bash runExamples.sh

echo ""
echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
sh runUnitTests.sh

echo "Tests completed without problem"
