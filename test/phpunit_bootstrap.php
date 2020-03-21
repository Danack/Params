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

/**
 * Used to convert to PHPUnits expected format.
 */
function stringToRegexp(string $string): string
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

function json_decode_safe($json)
{
    if ($json === null) {
        throw new \Exception("Error decoding JSON: cannot decode null.");
    }

    $data = json_decode($json, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        return $data;
    }

    throw new \Exception("Failed to decode json: " . json_last_error_msg());
//    $parser = new \Seld\JsonLint\JsonParser();
//    $parsingException = $parser->lint($json);
//
//    if ($parsingException !== null) {
//        throw $parsingException;
//    }
//
//    if ($data === null) {
//        throw new \Osf\Exception\JsonException("Error decoding JSON: null returned.");
//    }

//    throw new \Osf\Exception\JsonException("Error decoding JSON: " . json_last_error_msg());
}


function json_encode_safe($data, $options = 0): string
{
    $result = json_encode($data, $options);

    if ($result === false) {
        throw new \Exception("Failed to encode data as json: " . json_last_error_msg());
    }

    return $result;
}
