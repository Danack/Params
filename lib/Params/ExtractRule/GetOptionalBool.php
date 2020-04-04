<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\DataLocator;
use Params\ProcessRule\BoolInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;

/**
 *
 * If a parameter is not set, then the value is null, otherwise
 * it must be a valid integer.
 *
 */
class GetOptionalBool implements ExtractRule
{
    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues,
        DataLocator $dataLocator
    ): ValidationResult {

        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }
//        if ($varMap->has($path->toString()) !== true) {
//            return ValidationResult::valueResult(null);
//        }

        $intRule = new BoolInput();
        return $intRule->process(
            $path,
//            $varMap->get($path->toString()),
            $dataLocator->getCurrentValue(),
            $paramValues,
            $dataLocator
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(false);
    }
}
