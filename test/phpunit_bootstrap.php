<?php

//use Auryn\Injector;
//
//require_once(__DIR__.'/../vendor/autoload.php');
//require_once __DIR__ . '/injectionParamsCliTest.php';
//require_once __DIR__ . '/../lib/factories.php';
//require_once __DIR__ . '/../lib/functions.php';
//require_once __DIR__ . '/../lib/functions_test.php';
//require_once(__DIR__ . '/../vendor/fzaninotto/faker/src/autoload.php');
//


function stringToRegexp(string $string)
{
    $string = preg_quote($string, '#');

    $replacements = [
        '%s' => '.*',   // strings can be empty, so *
        '%d' => '\d+',  // numbers can't be empty so +
    ];

    $string = str_replace(
        array_keys($replacements),
        array_values($replacements),
        $string
    );

    return '#' . $string . '#iu';
}