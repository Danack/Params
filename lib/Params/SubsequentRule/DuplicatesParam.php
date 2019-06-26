<?php

declare(strict_types = 1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\ParamsValidator;
use Params\OpenApi\ParamDescription;

class DuplicatesParam implements SubsequentRule
{
    /** @var string  */
    private $paramToDuplicate;

    public const ERROR_NO_PREVIOUS_PARAM = "Param named %s was not previously processed.";

    public const ERROR_DIFFERENT_TYPES = "Parameter %s cannot by the same as %s as different types, %s and %s.";

    public const ERROR_DIFFERENT_VALUE = "Parameter named '%s' is different to parameter '%s'.";

    /**
     * @param string $paramName The name of the param this one should be the same as
     */
    public function __construct(string $paramToDuplicate)
    {
        $this->paramToDuplicate = $paramToDuplicate;
    }
    public function process(string $name, $value, ParamsValidator $validator) : ValidationResult
    {
        if ($validator->hasParam($this->paramToDuplicate) !== true) {
            $message = sprintf(
                self::ERROR_NO_PREVIOUS_PARAM,
                $this->paramToDuplicate
            );

            return ValidationResult::errorResult($message);
        }

        $previousValue = $validator->getParam($this->paramToDuplicate);

        $previousType = gettype($previousValue);
        $currentType =  gettype($value);

        if ($previousType !== $currentType) {
            $message = sprintf(
                self::ERROR_DIFFERENT_TYPES,
                $name,
                $this->paramToDuplicate,
                $previousType,
                $currentType
            );

            return ValidationResult::errorResult($message);
        }

        if ($value !== $previousValue) {
            $message = sprintf(
                self::ERROR_DIFFERENT_VALUE,
                $name,
                $this->paramToDuplicate
            );
            return ValidationResult::errorResult($message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
//        $paramDescription->setDescription("must be duplicate of $this->paramToDuplicate");
        $paramDescription->setExclusiveMaximum(false);
    }
}
