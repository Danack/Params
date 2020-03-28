<?php

declare(strict_types=1);

namespace Params\ProcessRule;

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

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        // TODO - handle to string conversion better.
        $value = (string)$value;
        if (strlen($value) < $this->minLength) {
            return ValidationResult::errorResult(
                $path,
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
