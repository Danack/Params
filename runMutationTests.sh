#!/usr/bin/env bash

rm infection-log.txt

php vendor/bin/infection \
  --configuration=infection.json \
  --log-verbosity=default \
  --only-covered \
  --min-covered-msi=90

infection_exit_code=$?

# set -e
# set -x

if [ ! -f infection-log.txt ]; then
    echo "infection-log.txt log not generated."
    exit -1;
fi

if [ "$infection_exit_code" -ne "0" ]; then echo "Infection failed"; exit "$infection_exit_code"; fi

cat infection-log.txt

# This is here as the output of mutation tests can be confused
# with the output of the unit tests. This cost me 3 hours when I learnt that.
echo "*********************************"
echo "** End of mutation tests       **"
echo "*********************************"