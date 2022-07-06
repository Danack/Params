<?php

declare(strict_types = 1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\ProcessedValues;
use Type\PropertyRule;
use Type\ValidationResult;

/**
 * The first rule for a property of a type. It should extract the
 * initial value out of the InputStorage.
 *
 * @package Params
 */
interface ExtractPropertyRule extends PropertyRule
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
