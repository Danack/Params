<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\ValidationResult;
use VarMap\VarMap;

class GetString implements ExtractRule
{
    const ERROR_MESSAGE = 'Value is not set.';

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($name) !== true) {
            return ValidationResult::errorResult($name, self::ERROR_MESSAGE);
        }
        // TODO - reject bools/ints?

        $value = (string)$varMap->get($name);

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(true);
    }
}
