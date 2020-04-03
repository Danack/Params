<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\Messages;
use Params\ValidationResult;
use Params\ParamsValuesImpl;
use Params\OpenApi\ParamDescription;
use Params\ParamValues;
use Params\Path;

class DuplicatesParam implements ProcessRule
{
    private string $paramToDuplicate;

    /**
     * @param string $paramToDuplicate The name of the param this one should be the same as.
     */
    public function __construct(string $paramToDuplicate)
    {
        $this->paramToDuplicate = $paramToDuplicate;
    }

    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        if ($validator->hasParam($this->paramToDuplicate) !== true) {
            $message = sprintf(
                Messages::ERROR_NO_PREVIOUS_PARAM,
                $this->paramToDuplicate
            );

            return ValidationResult::errorResult($path, $message);
        }

        $previousValue = $validator->getParam($this->paramToDuplicate);

        $previousType = gettype($previousValue);
        $currentType =  gettype($value);

        if ($previousType !== $currentType) {
            $message = sprintf(
                Messages::ERROR_DIFFERENT_TYPES,
                $path->toString(),
                $this->paramToDuplicate,
                $previousType,
                $currentType
            );

            return ValidationResult::errorResult($path, $message);
        }

        if ($value !== $previousValue) {
            $message = sprintf(
                Messages::ERROR_DIFFERENT_VALUE,
                $path->toString(),
                $this->paramToDuplicate
            );
            return ValidationResult::errorResult($path, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
//        $paramDescription->setDescription("must be duplicate of $this->paramToDuplicate");
        $paramDescription->setExclusiveMaximum(false);
    }
}
