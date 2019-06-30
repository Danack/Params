<?php

declare(strict_types=1);

namespace Params\FirstRule;

use Params\SubsequentRule;
use Params\FirstRule\FirstRule;
use Params\SubsequentRule\IntegerInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class GetInt implements FirstRule
{
    const ERROR_MESSAGE = 'Value not set for %s.';

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ) : ValidationResult {
        if ($varMap->has($name) !== true) {
            $message = sprintf(self::ERROR_MESSAGE, $name);
            return ValidationResult::errorResult($message);
        }

        $intRule = new IntegerInput();

        return $intRule->process($name, $varMap->get($name), $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
