<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule\BoolInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

class GetBoolOrDefault implements ExtractRule
{
    private bool $default;

    /**
     * setOrDefaultValidator constructor.
     * @param bool $default The default value to use.
     */
    public function __construct(bool $default)
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

        $intRule = new BoolInput();
        return $intRule->process($identifier, $value, $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
