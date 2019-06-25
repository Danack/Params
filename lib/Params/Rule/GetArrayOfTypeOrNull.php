<?php

declare(strict_types = 1);

namespace Params\Rule;

use Params\SafeAccess;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Params;
use Params\ValidationErrors;

class GetArrayOfTypeOrNull extends GetArrayOfType implements Rule
{
    public function __construct(VarMap $variableMap, string $className)
    {
        parent::__construct($variableMap, $className);
    }

    public function __invoke(string $name, $_): ValidationResult
    {
        if ($this->variableMap->has($name) === false) {
            return ValidationResult::finalValueResult(null);
        }

        return parent::__invoke($name, $_);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        parent::updateParamDescription($paramDescription);
    }
}
