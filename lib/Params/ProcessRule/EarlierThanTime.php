<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

/**
 * Checks that one parameter represents an earlier time than
 * the given time
 */
class EarlierThanTime implements ProcessRule
{
    private \DateTimeInterface $compareTime;

    public function __construct(\DateTimeInterface $compareTime)
    {
        $this->compareTime = $compareTime;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {

        if (!($value instanceof \DateTimeInterface)) {

            $message = sprintf(
                Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE,
                gettype($value)
            );
            return ValidationResult::errorResult(
                $dataLocator,
                $message
            );
        }

        if ($value < $this->compareTime) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            Messages::TIME_MUST_BE_BEFORE_TIME,
            $this->getCompareTimeString()
        );

        return ValidationResult::errorResult($dataLocator, $message);
    }

    public function getCompareTimeString(): string
    {
        return $this->compareTime->format(\DateTime::RFC3339);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $message = sprintf(
            Messages::TIME_MUST_BE_BEFORE_TIME,
            $this->compareTime->format(\DateTime::RFC3339)
        );

        $paramDescription->setDescription($message);
    }
}
