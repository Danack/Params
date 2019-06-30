<?php

declare(strict_types = 1);

namespace Params\FirstRule;

use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;

class GetArrayOfTypeOrNull extends GetArrayOfType implements FirstRule
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ): ValidationResult {
        if ($varMap->has($name) === false) {
            return ValidationResult::finalValueResult(null);
        }

        return parent::process($name, $varMap, $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        parent::updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
