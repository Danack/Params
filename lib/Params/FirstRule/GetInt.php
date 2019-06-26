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

class GetInt implements FirstRule
{
    const ERROR_MESSAGE = 'Value not set for %s.';

    public function process(
        string $variableName,
        VarMap $varMap,
        ParamsValidator $validator
    ) : ValidationResult {
        if ($varMap->has($variableName) !== true) {
            $message = sprintf(self::ERROR_MESSAGE, $variableName);
            return ValidationResult::errorResult($message);
        }

        $intRule = new IntegerInput();

        return $intRule->process($variableName, $varMap->get($variableName), $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
