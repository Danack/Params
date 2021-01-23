<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\ProcessedValues;
use Params\Rule;
use Params\ValidationResult;

/**
 * The first rule for a parameter. It should extract the initial value
 * out of the InputStorage.
 * @package Params
 */
interface ExtractRule extends Rule
{
    /**
     * @param ProcessedValues $processedValues
     * @param InputStorage $dataLocator
     * @return ValidationResult
     */
    public function process(
        ProcessedValues $processedValues,
        InputStorage $dataLocator
    ) : ValidationResult;
}
