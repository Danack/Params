<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\ProcessRule;
use Params\ExtractRule\ExtractRule;
use Params\ProcessRule\IntegerInput;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;

class GetInt implements ExtractRule
{
    const ERROR_MESSAGE = 'Value not set.';

    public function process(
        string $identifier,
        VarMap $varMap,
        ParamValues $paramValues
    ) : ValidationResult {
        if ($varMap->has($identifier) !== true) {
            return ValidationResult::errorResult($identifier, self::ERROR_MESSAGE);
        }

        $intRule = new IntegerInput();

        return $intRule->process($identifier, $varMap->get($identifier), $paramValues);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
