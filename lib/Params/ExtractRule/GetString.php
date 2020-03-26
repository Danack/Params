<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\Path;

class GetString implements ExtractRule
{
    const ERROR_MESSAGE = 'Value is not set.';

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($path->toString()) !== true) {
            return ValidationResult::errorResult($path, self::ERROR_MESSAGE);
        }
        // TODO - reject bools/ints?

        $value = (string)$varMap->get($path->toString());

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(true);
    }
}
