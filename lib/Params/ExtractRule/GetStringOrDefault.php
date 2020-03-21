<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;

class GetStringOrDefault implements ExtractRule
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
        string $name,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($name) !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $value = (string)$varMap->get($name);

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
