<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
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
     * @param DataStorage $dataStorage
     * @return ValidationResult
     */
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult;
}
