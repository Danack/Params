<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\DataLocator;
use Params\ProcessRule;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class GetOptionalString implements ExtractRule
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

        // TODO - convert strings better.
        $value = (string)$dataLocator->getCurrentValue();

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(false);
    }
}
