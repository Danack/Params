<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Exception\InvalidDatetimeFormatException;
use function Params\checkAllowedFormatsAreStrings;
use function Params\getDefaultSupportedTimeFormats;

class GetDatetime implements ExtractRule
{
    /**
     * @var string[]
     */
    private array $allowedFormats;

    /**
     *
     * @param string[] $allowedFormats
     */
    public function __construct(array $allowedFormats = null)
    {
        if ($allowedFormats === null) {
            $this->allowedFormats = getDefaultSupportedTimeFormats();
            return;
        }

        $this->allowedFormats = checkAllowedFormatsAreStrings($allowedFormats);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $value = $dataStorage->getCurrentValue();

        if (is_array($value) === true) {
            return ValidationResult::errorResult($dataStorage, Messages::ERROR_DATETIME_MUST_START_AS_STRING);
        }

        if (is_scalar($value) !== true) {
            return ValidationResult::errorResult(
                $dataStorage,
                Messages::ERROR_DATETIME_MUST_START_AS_STRING,
            );
        }

        // TODO - reject bools/ints?
        // TODO - needs string input
        $value = (string)$dataStorage->getCurrentValue();

        foreach ($this->allowedFormats as $allowedFormat) {
            $dateTime = \DateTimeImmutable::createFromFormat($allowedFormat, $value);

            if ($dateTime instanceof \DateTimeImmutable) {
                return ValidationResult::valueResult($dateTime);
            }
        }

        return ValidationResult::errorResult($dataStorage, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(true);
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
    }
}
