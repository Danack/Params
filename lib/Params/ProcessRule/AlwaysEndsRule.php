<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;

/**
 * Used for testing.
 */
class AlwaysEndsRule implements ProcessRule
{
    private $finalResult;

    /**
     * @param mixed $finalResult
     */
    public function __construct($finalResult)
    {
        $this->finalResult = $finalResult;
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        return ValidationResult::finalValueResult($this->finalResult);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing.
    }
}
