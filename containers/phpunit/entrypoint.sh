#!/bin/sh -l

set -e
# set -x
# pwd
# ls -l

sh -c "echo '---Installing dependencies---' wtf"
composer install

sh -c "echo '---Running unit tests---'"

sh runTests.sh