<?php

declare(strict_types=1);

namespace TypeSpecExample;

use Respect\Validation\Validator as v;
use TypeSpec\ProcessRule\ProcessPropertyRule;
use TypeSpec\ValidationResult;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\DataStorage\DataStorage;

/**
 * This is an example of using a validator from
 * the Respect/Validation library
 */
class RespectMacRule implements ProcessPropertyRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ) : ValidationResult {
        if (v::macAddress()->validate($value) === true) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            "String [%s] is not a valid mac address.",
            substr($value, 0, 64)
        );

        return ValidationResult::errorResult($inputStorage, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setDescription("A string representing a MAC address.");
        // TODO - other settings
    }
}
