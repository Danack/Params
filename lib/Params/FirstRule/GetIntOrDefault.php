<?php

declare(strict_types=1);

namespace Params\FirstRule;

use Params\SubsequentRule\IntegerInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class GetIntOrDefault implements FirstRule
{
    private $default;

    /**
     * setOrDefaultValidator constructor.
     * @param mixed $default
     */
    public function __construct($default)
    {
        $this->default = $default;
    }

    public function process(
        string $variableName,
        VarMap $varMap,
        ParamsValidator $validator
    ): ValidationResult {
        if ($varMap->has($variableName) === true) {
            $value = $varMap->get($variableName);
        }
        else {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new IntegerInput();
        return $intRule->process($variableName, $value, $validator);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
