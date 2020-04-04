<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\DataLocator\DataLocator;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;

/**
 * This is a useless clas
 */
class GetArrayOfTypeOrNull extends GetArrayOfType implements ExtractRule
{
    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues,
        DataLocator $dataLocator
    ): ValidationResult {

        if ($varMap->has($path->getCurrentName()) === false) {
            return ValidationResult::valueResult(null);
        }

        return parent::process($path, $varMap, $paramValues, $dataLocator);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        parent::updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
