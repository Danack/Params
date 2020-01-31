#!/bin/sh -l

sh -c "echo '---Installing dependencies---'"
composer install

sh -c "echo '---Running unit tests---'"
vendor/bin/phpunit --testsuite=DrupalPatchChecker