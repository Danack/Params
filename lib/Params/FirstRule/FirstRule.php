<?php

declare(strict_types = 1);

namespace Params\FirstRule;

use Params\Rule;
use Params\ValidationResult;
use VarMap\VarMap;
use Params\ParamsValidator;
use Params\ParamValues;

/**
 * The first rule for a parameter. It should extract the initial value
 * out of the Variable Map.
 * @package Params
 */
interface FirstRule extends Rule
{
    /**
     * @param string $name The input variable name
     * @param VarMap $varMap
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(string $name, VarMap $varMap, ParamValues $validator) : ValidationResult;
}
