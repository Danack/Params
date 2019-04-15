#!/usr/bin/env bash

set -e
set -x


docker-compose exec -T php_test sh -c "sh runTests.sh"



