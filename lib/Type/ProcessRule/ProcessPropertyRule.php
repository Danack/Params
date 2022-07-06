<?php

declare(strict_types = 1);

namespace Type\ProcessRule;

use Type\DataStorage\DataStorage;
use Type\ProcessedValues;
use Type\PropertyRule;
use Type\ValidationResult;

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
     * @throws \Type\Exception\ParamMissingException
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult;
}
