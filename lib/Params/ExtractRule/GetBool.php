<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\ProcessRule\BoolInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class GetBool implements ExtractRule
{
    const ERROR_MESSAGE = 'Value not set.';

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($path->getCurrentName()) !== true) {
            return ValidationResult::errorResult($path->getCurrentName(), self::ERROR_MESSAGE);
        }

        $intRule = new BoolInput();
        return $intRule->process($path, $varMap->get($path->getCurrentName()), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(true);
    }
}
