<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\InputStorage\InputStorage;
use Params\ProcessedValues;
use Params\Rule;
use Params\ValidationResult;

/**
 * A rule for processing the value after it has been extracted from the
 * InputStorage by an ExtractRule.
 */
interface ProcessRule extends Rule
{
    /**
     * @param mixed $value The current value of the param as it is being processed.
     * @param ProcessedValues $processedValues The already processed parameters.
     * @param InputStorage $inputStorage The InputStorage with the current path set to the
     *   appropriate place to find the current value by calling $inputStorage->getCurrentValue()
     * @return ValidationResult
     * @throws \Params\Exception\ParamMissingException
     */
    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorage $inputStorage
    ): ValidationResult;
}
