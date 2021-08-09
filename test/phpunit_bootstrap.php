<?php


use Params\ExtractRule\GetInt;
use Params\InputParameter;
use Params\ProcessedValue;
use Params\ProcessedValues;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/fixtures.php";



function createProcessedValuesFromArray(array $keyValues): ProcessedValues
{
    $processedValues = [];

    foreach ($keyValues as $key => $value) {
        $extractRule = new GetInt();
        $inputParameter = new InputParameter($key, $extractRule);
        $processedValues[] = new ProcessedValue($inputParameter, $value);
    }

    return ProcessedValues::fromArray($processedValues);
}