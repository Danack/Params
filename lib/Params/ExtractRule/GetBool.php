<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\ProcessRule\BoolInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class GetBool implements ExtractRule
{
    const ERROR_MESSAGE = 'Value not set.';

    public function __construct(string $key)
    {
    }

    // TODO - why is name needed here also, as well as in constructor?
    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {
        if ($varMap->has($name) !== true) {
            return ValidationResult::errorResult($name, self::ERROR_MESSAGE);
        }

        $intRule = new BoolInput();
        return $intRule->process($name, $varMap->get($name), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setRequired(true);
    }
}
