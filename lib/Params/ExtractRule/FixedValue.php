<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class FixedValue implements ExtractRule
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
