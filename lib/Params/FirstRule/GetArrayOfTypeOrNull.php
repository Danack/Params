<?php

declare(strict_types = 1);

namespace Params\FirstRule;

use Params\FirstRule\GetArrayOfType;
use Params\SafeAccess;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\SubsequentRule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Params;
use Params\ValidationErrors;
use Params\ParamsValidator;

class GetArrayOfTypeOrNull extends GetArrayOfType implements FirstRule
{
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function process(
        string $variableName,
        VarMap $varMap,
        ParamsValidator $validator
    ): ValidationResult {
        if ($varMap->has($variableName) === false) {
            return ValidationResult::finalValueResult(null);
        }

        return parent::process($variableName, $varMap, $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        parent::updateParamDescription($paramDescription);
    }
}
