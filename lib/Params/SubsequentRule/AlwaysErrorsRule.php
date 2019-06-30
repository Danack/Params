<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class AlwaysErrorsRule implements SubsequentRule
{
    /** @var string */
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }


    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        return ValidationResult::errorResult($this->message);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // Does nothing.
    }
}
