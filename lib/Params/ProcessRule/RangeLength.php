<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\DataLocator\DataLocator;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class RangeLength implements ProcessRule
{
    private int $minLength;

    private int $maxLength;

    /**
     * MaxLengthValidator constructor.
     * @param int $maxLength
     */
    public function __construct(int $minLength, int $maxLength)
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    public function process(Path $path, $value, ParamValues $validator, DataLocator $dataLocator) : ValidationResult
    {
        // TODO - handle to string conversion better.
        $value = (string)$value;

        // Check min length
        if (mb_strlen($value) < $this->minLength) {
            return ValidationResult::errorResult(
                $dataLocator,
                "String too short, min chars is " . $this->minLength
            );
        }

        // Check max length
        if (mb_strlen($value) > $this->maxLength) {
            $message = sprintf(
                "String too long for '%s', max chars is %d.",
                $path->toString(),
                $this->maxLength
            );
            return ValidationResult::errorResult($dataLocator, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMaxLength($this->maxLength);
    }
}
