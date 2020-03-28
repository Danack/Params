<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule\IntegerInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class GetIntOrDefault implements ExtractRule
{
    private ?int $default;

    /**
     * @param ?int $default
     */
    public function __construct(?int $default)
    {
        $this->default = $default;
    }

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($path->toString()) === true) {
            $value = $varMap->get($path->toString());
        }
        else {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new IntegerInput();
        return $intRule->process($path, $value, $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
