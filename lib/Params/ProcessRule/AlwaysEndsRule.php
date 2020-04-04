<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\DataLocator\DataLocator;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;

/**
 * Used for testing.
 */
class AlwaysEndsRule implements ProcessRule
{
    /** @var mixed */
    private $finalValue;

    /**
     * @param mixed $finalResult
     */
    public function __construct($finalResult)
    {
        $this->finalValue = $finalResult;
    }

    /**
     * @param Path $path
     * @param mixed $value
     * @param ParamValues $validator
     * @return ValidationResult
     */
    public function process(Path $path, $value, ParamValues $validator, DataLocator $dataLocator) : ValidationResult
    {
        return ValidationResult::finalValueResult($this->finalValue);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing.
    }
}
