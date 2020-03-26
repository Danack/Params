<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class AlwaysErrorsRule implements ProcessRule
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }


    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        return ValidationResult::errorResult($path, $this->message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // Does nothing.
    }
}
