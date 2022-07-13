<?php

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessedValue;
use TypeSpec\ProcessedValues;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/fixtures.php";

function createProcessedValuesFromArray(array $keyValues): ProcessedValues
{
    $processedValues = [];

    foreach ($keyValues as $key => $value) {
        $extractRule = new GetInt();
        $inputParameter = new InputTypeSpec($key, $extractRule);
        $processedValues[] = new ProcessedValue($inputParameter, $value);
    }

    return ProcessedValues::fromArray($processedValues);
}