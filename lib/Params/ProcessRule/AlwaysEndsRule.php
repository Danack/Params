<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;

/**
 * Used for testing.
 */
class AlwaysEndsRule implements ProcessRule
{
    private $finalResult;

    public function __construct($finalResult)
    {
        $this->finalResult = $finalResult;
    }

    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        return ValidationResult::finalValueResult($this->finalResult);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // Does nothing.
    }
}
