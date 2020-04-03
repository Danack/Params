<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\Messages;
use Params\ProcessRule;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\FloatInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class GetFloat implements ExtractRule
{

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ) : ValidationResult {
        // TODO - this is an error. It should be getCurrentName
        // Fix this after writing a test to detect this type of issue.
        if ($varMap->has($path->toString()) !== true) {
            return ValidationResult::errorResult($path, Messages::VALUE_NOT_SET);
        }

        $intRule = new FloatInput();

        return $intRule->process($path, $varMap->get($path->getCurrentName()), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(true);
    }
}
