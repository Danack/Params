<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\DataLocator\DataLocator;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class MinLength implements ProcessRule
{
    private int $minLength;

    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    public function process(Path $path, $value, ParamValues $validator, DataLocator $dataLocator) : ValidationResult
    {
        // TODO - handle to string conversion better.
        $value = (string)$value;
        if (mb_strlen($value) < $this->minLength) {
            return ValidationResult::errorResult(
                $dataLocator,
                "String too short, min chars is " . $this->minLength
            );
        }
        return ValidationResult::valueResult($value);
    }


    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMinLength($this->minLength);
    }
}
