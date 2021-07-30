<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataStorage\DataStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class Trim implements ProcessRule
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
