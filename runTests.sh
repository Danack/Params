#!/usr/bin/env bash

set -e
# set -x

sh runCodeSniffer.sh

sh runUnitTests.sh

php ./phpstan.phar analyze -c ./phpstan.neon -l 7 lib

set +e

php vendor/bin/infection --configuration=infection.json.dist --log-verbosity=0 --only-covered --min-covered-msi=90

infection_exit_code=$?

set -e

if [ "$infection_exit_code" -ne "0" ]; then echo "Infection failed"; cat infection-log.txt;  exit "$infection_exit_code"; fi

sh runExamples.sh

echo "Tests completed without problem"

# rerun unit tests to get the stats again, to save scrolling...
php vendor/bin/phpunit -c test/phpunit.xml
