<?php

declare(strict_types=1);

namespace Params\ProcessRule;

use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ParamValues;
use Params\Path;

class MaxLength implements ProcessRule
{
    private int $maxLength;

    /**
     * MaxLengthValidator constructor.
     * @param int $maxLength
     */
    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }
    public function process(Path $path, $value, ParamValues $validator) : ValidationResult
    {
        // TODO - handle to string conversion better.

        if (strlen((string)$value) > $this->maxLength) {
            $message = sprintf(
                "String too long for '%s', max chars is %d.",
                $path->toString(),
                $this->maxLength
            );
            return ValidationResult::errorResult($path, $message);
        }

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setMaxLength($this->maxLength);
    }
}
