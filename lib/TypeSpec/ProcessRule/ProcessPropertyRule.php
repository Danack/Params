<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\ProcessedValues;
use TypeSpec\PropertyRule;
use TypeSpec\ValidationResult;

/**
 * A rule for processing the value after it has been extracted from the
 * InputStorage by an ExtractRule.
 */
interface ProcessPropertyRule extends PropertyRule
{
    /**
     * @param mixed $value The current value of the param as it is being processed.
     * @param ProcessedValues $processedValues The already processed parameters.
     * @param DataStorage $inputStorage The InputStorage with the current path set to the
     *   appropriate place to find the current value by calling $inputStorage->getCurrentValue()
     * @return ValidationResult
     * @throws \TypeSpec\Exception\ParamMissingException
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult;
}
