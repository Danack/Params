<?php

declare(strict_types = 1);

namespace Params\ProcessRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Exception\LogicException;

class LaterThanParam implements ProcessRule
{
    private string $paramToCompare;

    private int $minutesLater;

    /**
     * @param string $paramToCompare The name of the param this one should be the same as.
     * @param int $minutesLater how many minutes later this time needs to be
     */
    public function __construct(string $paramToCompare, int $minutesLater)
    {
        $this->paramToCompare = $paramToCompare;
        $this->minutesLater = $minutesLater;

        if ($minutesLater < 0) {
            throw new LogicException("minutes later must be >= 0");
        }
    }


    public function process(
        $value,
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {
        if ($processedValues->hasValue($this->paramToCompare) !== true) {
            $message = sprintf(
                Messages::ERROR_NO_PREVIOUS_PARAM,
                $this->paramToCompare
            );

            return ValidationResult::errorResult($dataLocator, $message);
        }

        $previousValue = $processedValues->getValue($this->paramToCompare);

        if (!($previousValue instanceof \DateTimeInterface)) {
            $message = sprintf(
                Messages::PREVIOUS_TIME_MUST_BE_DATETIMEINTERFACE,
                $this->paramToCompare
            );

            return ValidationResult::errorResult($dataLocator, $message);
        }

        if (!($value instanceof \DateTimeInterface)) {
            return ValidationResult::errorResult(
                $dataLocator,
                Messages::CURRENT_TIME_MUST_BE_DATETIMEINTERFACE
            );
        }

        $timeOffset = new \DateInterval('PT'  . $this->minutesLater . 'M');

        /** @var \DateTimeImmutable|\DateTime $previousValue */
        // @phpstan-ignore-next-line
        $timeToCompare = $previousValue->add($timeOffset);


        if ($value > $timeToCompare) {
            return ValidationResult::valueResult($value);
        }

        $message = sprintf(
            Messages::TIME_MUST_BE_X_MINUTES_AFTER_TIME,
            $this->minutesLater,
            $this->paramToCompare,
            $previousValue->format(\DateTime::RFC3339)
        );

        return ValidationResult::errorResult($dataLocator, $message);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $message = sprintf(
            Messages::TIME_MUST_BE_X_MINUTES_AFTER_PREVIOUS_VALUE,
            $this->minutesLater,
            $this->paramToCompare
        );

        $paramDescription->setDescription($message);
    }
}
