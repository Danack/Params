<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\DataLocator\DataLocator;
use Params\Rule;
use Params\ValidationResult;
use Params\ParamValues;
use VarMap\VarMap;
use Params\Path;

/**
 * The first rule for a parameter. It should extract the initial value
 * out of the Variable Map.
 * @package Params
 */
interface ExtractRule extends Rule
{
    /**
     * @param \Params\Path $path
     * @param VarMap $varMap The variable map containing the variables
     * @param ParamValues $paramValues
     * @param DataLocator $dataLocator
     * @return ValidationResult
     */
    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues,
        DataLocator $dataLocator
    ) : ValidationResult;
}
