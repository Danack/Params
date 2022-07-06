<?php


use Type\ExtractRule\GetInt;
use Type\PropertyDefinition;
use Type\ProcessedValue;
use Type\ProcessedValues;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/fixtures.php";



function createProcessedValuesFromArray(array $keyValues): ProcessedValues
{
    $processedValues = [];

    foreach ($keyValues as $key => $value) {
        $extractRule = new GetInt();
        $inputParameter = new PropertyDefinition($key, $extractRule);
        $processedValues[] = new ProcessedValue($inputParameter, $value);
    }

    return ProcessedValues::fromArray($processedValues);
}