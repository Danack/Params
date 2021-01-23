<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class Trim implements ProcessRule
{
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult {

        // TODO - handle string conversion more safely?
        return ValidationResult::valueResult(trim((string)$value));
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Should update description?
    }
}
