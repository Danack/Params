<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\FloatInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
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
        string $name,
        VarMap $varMap,
        ParamValues $paramValues
    ) : ValidationResult {

        if ($varMap->has($name) === true) {
            $value = $varMap->get($name);
        }
        else {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new FloatInput();

        return $intRule->process($name, $varMap->get($name), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(true);
    }
}
