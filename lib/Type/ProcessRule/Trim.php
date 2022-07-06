<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class Trim implements ProcessPropertyRule
{
    use CheckString;

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        $value = $this->checkString($value);


        return ValidationResult::valueResult(trim($value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Should update description?
    }
}
