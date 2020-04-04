<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\DataLocator;
use Params\Messages;
use Params\ProcessRule;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\IntegerInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class GetInt implements ExtractRule
{
    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues,
        DataLocator $dataLocator
    ) : ValidationResult {
//        if ($varMap->has($path->getCurrentName()) !== true) {
//            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
//        }

        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
        }

        $intRule = new IntegerInput();
        $value = $dataLocator->getCurrentValue();
//        $value = $varMap->get($path->getCurrentName());
        return $intRule->process($path, $value, $paramValues, $dataLocator);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
