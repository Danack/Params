<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\ParamsValidator;
use Params\Rule;
use Params\ValidationResult;
use Params\ParamValues;

/**
 * A rule that is not the first rule. It should process the value that is passed to it.
 * @package Params
 */
interface ProcessRule extends Rule
{
    /**
     * @param string $name The name of the param being processed.
     * @param mixed $value The current value of the param as it is being processed
     * @param ParamValues $validator The name of the param being processed.
     * @return ValidationResult
     * @throws \Params\Exception\ParamMissingException
     */
    public function process(string $name, $value, ParamValues $validator) : ValidationResult;
}
