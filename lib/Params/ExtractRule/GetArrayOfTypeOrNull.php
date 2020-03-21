<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;

class GetArrayOfTypeOrNull extends GetArrayOfType implements ExtractRule
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($name) === false) {
            return ValidationResult::finalValueResult(null);
        }

        return parent::process($name, $varMap, $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        parent::updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
