<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\Rule;
use Params\ValidationResult;
use Params\ParamValues;
use VarMap\VarMap;

/**
 * The first rule for a parameter. It should extract the initial value
 * out of the Variable Map.
 * @package Params
 */
interface ExtractRule extends Rule
{
    /**
     * @param string $identifier The input variable name
     * @param VarMap $varMap The variable map containing the variables
     * @param ParamValues $paramValues
     * @return ValidationResult
     */
    public function process(
        string $identifier,
        VarMap $varMap,
        ParamValues $paramValues
    ) : ValidationResult;
}
