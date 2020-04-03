<?php

declare(strict_types=1);

namespace Params\ExtractRule;

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
        ParamValues $paramValues
    ) : ValidationResult {
        if ($varMap->has($path->getCurrentName()) !== true) {
            return ValidationResult::errorResult($path, Messages::VALUE_NOT_SET);
        }

        $intRule = new IntegerInput();

        return $intRule->process($path, $varMap->get($path->getCurrentName()), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
