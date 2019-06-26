<?php

declare(strict_types=1);

namespace Params\FirstRule;

use Params\FirstRule\FirstRule;
use Params\SubsequentRule;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class GetString implements FirstRule
{
    const ERROR_MESSAGE = 'Value not set for %s.';

    public function process(
        string $variableName,
        VarMap $varMap,
        ParamsValidator $validator
    ): ValidationResult {
        if ($varMap->has($variableName) !== true) {
            $message = sprintf(self::ERROR_MESSAGE, $variableName);
            return ValidationResult::errorResult($message);
        }
        // TODO - reject bools/ints?

        $value = (string)$varMap->get($variableName);

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(true);
    }
}
