<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\ParamsValuesImpl;
use Params\Rule;
use Params\ValidationResult;
use Params\ParamValues;
use Params\Path;
use Params\DataLocator\DataLocator;

/**
 * A rule that is not the first rule. It should process the value that is passed to it.
 * @package Params
 */
interface ProcessRule extends Rule
{
    /**
     * @param Path $path
     * @param mixed $value The current value of the param as it is being processed
     * @param ParamValues $validator The name of the param being processed.
     * @return ValidationResult
     * @throws \Params\Exception\ParamMissingException
     */
    public function process(Path $path, $value, ParamValues $validator, DataLocator $dataLocator) : ValidationResult;
}
