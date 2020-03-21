#!/bin/sh -l

set -e
# set -x
# pwd
# ls -l

echo '---Installing dependencies---'
php ./composer.phar install

sh runCodeSniffer.sh

# https://help.github.com/en/actions/automating-your-workflow-with-github-actions/development-tools-for-github-actions#set-an-error-message-error
# echo "::error file=app.js,line=10,col=15::Something went wrong"

echo "---fin---"
