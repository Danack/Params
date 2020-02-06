<?php

declare(strict_types=1);

namespace Params\FirstRule;

use Params\SubsequentRule;
use Params\FirstRule\FirstRule;
use Params\SubsequentRule\FloatInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class GetFloat implements FirstRule
{
    const ERROR_MESSAGE = 'Value not set.';

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ) : ValidationResult {
        if ($varMap->has($name) !== true) {
            return ValidationResult::errorResult($name, self::ERROR_MESSAGE);
        }

        $intRule = new FloatInput();

        return $intRule->process($name, $varMap->get($name), $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(true);
    }
}
