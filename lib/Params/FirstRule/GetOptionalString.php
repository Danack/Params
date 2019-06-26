<?php

declare(strict_types=1);

namespace Params\FirstRule;

use Params\SubsequentRule;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class GetOptionalString implements FirstRule
{
    const ERROR_MESSAGE = 'Value not set for %s.';

    public function process(
        string $variableName,
        VarMap $varMap,
        ParamsValidator $validator
    ): ValidationResult {
        if ($varMap->has($variableName) !== true) {
            return ValidationResult::valueResult(null);
        }

        $value = (string)$varMap->get($variableName);

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(false);
    }
}
