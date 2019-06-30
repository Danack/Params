<?php

declare(strict_types=1);

namespace Params\SubsequentRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValidator;
use Params\ParamValues;

class MaxLength implements SubsequentRule
{
    private $maxLength;

    /**
     * MaxLengthValidator constructor.
     * @param int $maxLength
     */
    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }
    public function process(string $name, $value, ParamValues $validator) : ValidationResult
    {
        if (strlen($value) > $this->maxLength) {
            $message = sprintf(
                "String too long for '%s', max chars is %d.",
                $name,
                $this->maxLength
            );
            return ValidationResult::errorResult($message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        $paramDescription->setMaxLength($this->maxLength);
    }
}
