<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule;
use Params\ProcessRule\IntegerInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

/**
 * Class GetOptionalInt
 *
 * If a parameter is not set, then the value is null, otherwise
 * it must be a valid integer.
 *
 */
class GetOptionalInt implements ExtractRule
{
    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($path->toString()) !== true) {
            return ValidationResult::valueResult(null);
        }

        $intRule = new IntegerInput();
        return $intRule->process($path, $varMap->get($path->toString()), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(false);
    }
}
