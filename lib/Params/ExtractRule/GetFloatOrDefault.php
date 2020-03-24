<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\FloatInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

class GetFloatOrDefault implements ExtractRule
{
    const ERROR_MESSAGE = 'Value not set.';

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
    ) : ValidationResult {

        if ($varMap->has($identifier) !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $floatInput = new FloatInput();

        return $floatInput->process($identifier, $varMap->get($identifier), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(true);
    }
}
