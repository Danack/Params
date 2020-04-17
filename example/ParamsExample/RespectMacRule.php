<?php

declare(strict_types=1);

namespace ParamsExample;

use Respect\Validation\Validator as v;
use Params\Path;
use Params\ProcessRule\ProcessRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\DataLocator\InputStorageAye;

/**
 * This is an example of using a validator from
 * the Respect/Validation library
 */
class RespectMacRule implements ProcessRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ) : ValidationResult {
        if (v::macAddress()->validate($value) === true) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            "String [%s] is not a valid mac address.",
            substr($value, 0, 64)
        );

        return ValidationResult::errorResult($dataLocator, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setDescription("A string representing a MAC address.");
        // TODO - other settings
    }
}
