# Params

Framework agnostic conversion of variables into types objects.

[![Build Status](https://travis-ci.org/Danack/Params.svg?branch=master)](https://travis-ci.org/Danack/Params)


# Types


## Ordering



## Tests

We have several tools that are run to improve code quality.

Standard unit tests:

```
php vendor/bin/phpunit -c test/phpunit.xml
```


Code sniffer for code styling.

```
php vendor/bin/phpcs --standard=./test/codesniffer.xml --encoding=utf-8 --extensions=php -p -s lib

```

Copy and paste detector.
```
php phpcpd-4.0.0.phar --regexps-exclude="#.*vendor.*#" --min-lines=3 --min-tokens=30 LegacyScredible/api
```

In the FoobarServer directory
```
php phpstan.phar analyze -c ./phpstan.neon -l 4 lib
```