<?php

declare(strict_types=1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class FixedValue implements ExtractPropertyRule
{
    private mixed $value;

    /**
     * @param mixed $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {

        return ValidationResult::valueResult($this->value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
    }
}
