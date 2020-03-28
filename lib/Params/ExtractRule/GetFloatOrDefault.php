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
use Params\Path;

class GetFloatOrDefault implements ExtractRule
{
    const ERROR_MESSAGE = 'Value not set.';

    private ?float $default;

    /**
     * @param float $default
     */
    public function __construct(?float  $default)
    {
        $this->default = $default;
    }

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ) : ValidationResult {

        if ($varMap->has($path->toString()) !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $floatInput = new FloatInput();

        return $floatInput->process($path, $varMap->get($path->toString()), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(true);
    }
}
