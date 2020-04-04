<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\DataLocator;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\Path;

class GetString implements ExtractRule
{

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues,
        DataLocator $dataLocator
    ): ValidationResult {
        if ($varMap->has($path->getCurrentName()) !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
        }
        // TODO - reject bools/ints?
        $value = (string)$varMap->get($path->getCurrentName());

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setRequired(true);
    }
}
