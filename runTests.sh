#!/usr/bin/env bash

set -e
set -x

bash runUnitTests.sh --no-coverage


bash runCodeSniffer.sh

echo "Running PHPStan"
php vendor/bin/phpstan analyze -c ./phpstan.neon -l 8 src

echo "Running Psalm"
php ./psalm.phar

# Exclude mutation tests for now.
# They are disabled as they are very slow to run, and not
# providing much value. I'm open to most pull requests that fix any of them
# or disabling ones that aren't that great.
# bash runMutationTests.sh

bash runExamples.sh

echo ""
echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
sh runUnitTests.sh

echo "Tests completed without problem"
