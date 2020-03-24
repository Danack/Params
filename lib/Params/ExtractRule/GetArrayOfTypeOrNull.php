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
        string $identifier,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {

        // If it's
        if ($varMap->has($identifier) === false) {
            return ValidationResult::valueResult(null);
        }

        return parent::process($identifier, $varMap, $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        parent::updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
