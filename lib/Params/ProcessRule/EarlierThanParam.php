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
 * Checks that one parameter represents an earlier time than another parameter
 */
class EarlierThanParam implements ProcessRule
{
    private string $paramToCompare;

    private int $minutesEarlier;

    /**
     * @param string $paramToCompare The name of the param this one should be the same as.
     * @param int $minutesEarlier how many minutes later this time needs to be
     */
    public function __construct(string $paramToCompare, int $minutesEarlier)
    {
        $this->paramToCompare = $paramToCompare;
        $this->minutesEarlier = $minutesEarlier;

        if ($minutesEarlier < 0) {
            throw new LogicException(Messages::MINUTES_MUST_BE_GREATER_THAN_ZERO);
        }
    }


    public function process(
        $value,
        ProcessedValues $processedValues,
        DataStorage $inputStorage
    ): ValidationResult {
        if ($processedValues->hasValue($this->paramToCompare) !== true) {
            $message = sprintf(
                Messages::ERROR_NO_PREVIOUS_PARAM,
                $this->paramToCompare
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        $previousValue = $processedValues->getValue($this->paramToCompare);

        if (!($previousValue instanceof \DateTimeInterface)) {
            $message = sprintf(
                Messages::PREVIOUS_TIME_MUST_BE_DATETIMEINTERFACE,
                $this->paramToCompare
            );

            return ValidationResult::errorResult($inputStorage, $message);
        }

        if (!($value instanceof \DateTimeInterface)) {
            return ValidationResult::errorResult(
                $inputStorage,
                Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE
            );
        }

        $timeOffset = new \DateInterval('PT'  . $this->minutesEarlier . 'M');

        /** @var \DateTimeImmutable|\DateTime $previousValue */
        $timeToCompare = $previousValue->add($timeOffset);

        if ($value > $timeToCompare) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            Messages::TIME_MUST_BE_X_MINUTES_BEFORE_PARAM_ERROR,
            $this->minutesEarlier,
            $this->paramToCompare,
            $previousValue->format(\DateTime::RFC3339)
        );

        return ValidationResult::errorResult($inputStorage, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $message = sprintf(
            Messages::TIME_MUST_BE_X_MINUTES_BEFORE_PARAM,
            $this->minutesEarlier,
            $this->paramToCompare
        );

        $paramDescription->setDescription($message);
    }
}
