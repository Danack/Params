<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule\IntegerInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

class GetIntOrDefault implements ExtractRule
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
        string $identifier,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($identifier) === true) {
            $value = $varMap->get($identifier);
        }
        else {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new IntegerInput();
        return $intRule->process($identifier, $value, $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
