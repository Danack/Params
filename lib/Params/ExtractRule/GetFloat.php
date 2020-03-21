<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\FloatInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class GetFloat implements ExtractRule
{
    const ERROR_MESSAGE = 'Value not set.';

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $paramValues
    ) : ValidationResult {
        if ($varMap->has($name) !== true) {
            return ValidationResult::errorResult($name, self::ERROR_MESSAGE);
        }

        $intRule = new FloatInput();

        return $intRule->process($name, $varMap->get($name), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(true);
    }
}
