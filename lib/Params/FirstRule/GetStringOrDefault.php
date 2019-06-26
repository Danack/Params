<?php

declare(strict_types=1);

namespace Params\FirstRule;

use Params\SubsequentRule;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;

class GetStringOrDefault implements FirstRule
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
        if ($varMap->has($variableName) !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $value = (string)$varMap->get($variableName);

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
