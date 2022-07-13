<?php

declare(strict_types = 1);

namespace TypeSpec\ProcessRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;

/**
 * Checks that one parameter represents an earlier time than
 * the given time
 */
class EarlierThanTime implements ProcessPropertyRule
{
    private \DateTimeInterface $compareTime;

    public function __construct(\DateTimeInterface $compareTime)
    {
        $this->compareTime = $compareTime;
    }

    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {

        if (!($value instanceof \DateTimeInterface)) {
            $message = sprintf(
                Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE,
                gettype($value)
            );
            return ValidationResult::errorResult(
                $inputStorage,
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

        return ValidationResult::errorResult($inputStorage, $message);
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
