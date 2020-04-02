#!/usr/bin/env bash

set -e
# set -x

sh runCodeSniffer.sh

sh runUnitTests.sh --no-coverage

php ./phpstan.phar analyze -c ./phpstan.neon -l 7 lib

php ./psalm.phar

sh runMutationTests.sh

sh runExamples.sh

echo ""
echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
sh runUnitTests.sh

echo "Tests completed without problem"
