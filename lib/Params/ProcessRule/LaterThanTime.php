<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Exception\LogicException;

/**
 * Checks that one parameter represents an earlier time than
 * the given time
 */
class LaterThanTime implements ProcessRule
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
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE
            );
        }

        if ($value > $this->compareTime) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            Messages::TIME_MUST_BE_AFTER_TIME,
            $this->compareTime->format(\DateTime::RFC3339)
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
            Messages::TIME_MUST_BE_AFTER_TIME,
            $this->compareTime->format(\DateTime::RFC3339)
        );

        $paramDescription->setDescription($message);
    }
}
