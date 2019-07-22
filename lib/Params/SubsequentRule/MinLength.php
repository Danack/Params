<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class MinLength implements SubsequentRule
{
    /** @var int  */
    private $minLength;

    public function __construct(int $minLength)
    {
        $this->minLength = $minLength;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (strlen($value) < $this->minLength) {
            return ValidationResult::errorResult(
                $name,
                "String too short, min chars is " . $this->minLength
            );
        }
        return ValidationResult::valueResult($value);
    }


    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMinLength($this->minLength);
    }
}
