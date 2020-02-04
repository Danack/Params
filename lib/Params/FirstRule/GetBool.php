<?php

declare(strict_types = 1);

namespace Params\FirstRule;

use Params\SubsequentRule\BoolInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class GetBool implements FirstRule
{
    const ERROR_MESSAGE = 'Value not set.';

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ): ValidationResult
    {
        if ($varMap->has($name) !== true) {
            return ValidationResult::errorResult($name, self::ERROR_MESSAGE);
        }

        $intRule = new BoolInput();
        return $intRule->process($name, $varMap->get($name), $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(true);
    }
}

