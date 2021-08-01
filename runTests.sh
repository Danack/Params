#!/usr/bin/env bash

set -e
set -x

echo "**************DJA";
time bash runUnitTests.sh --no-coverage



bash runCodeSniffer.sh

echo "Running PHPStan"
php ./phpstan.phar analyze -c ./phpstan.neon -l 8 lib

echo "Running Psalm"
php ./psalm.phar

# Exclude mutation tests for now.
# bash runMutationTests.sh

bash runExamples.sh

echo ""
echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
sh runUnitTests.sh

echo "Tests completed without problem"
