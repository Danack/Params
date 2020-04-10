<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
use Params\ProcessedValues;
use Params\Rule;
use Params\ValidationResult;


/**
 * A rule that is not the first rule. It should process the value that is passed to it.
 * @package Params
 */
interface ProcessRule extends Rule
{
    /**
     * @param mixed $value The current value of the param as it is being processed
     * @param ProcessedValues $processedValues
     * @param InputStorageAye $dataLocator
     * @return ValidationResult
     * @throws \Params\Exception\ParamMissingException
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult;
}
