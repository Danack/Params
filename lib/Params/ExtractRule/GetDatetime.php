<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use Params\Exception\InvalidDatetimeFormatException;

class GetDatetime implements ExtractRule
{
    private array $allowedFormats;

    /**
     *
     * @param string[] $allowedFormats
     */
    public function __construct(array $allowedFormats = null)
    {
        if ($allowedFormats === null) {
            $allowedFormats = [
                \DateTime::ATOM,
                \DateTime::COOKIE,
                \DateTime::ISO8601,
                \DateTime::RFC822,
                \DateTime::RFC850,
                \DateTime::RFC1036,
                \DateTime::RFC1123,
                \DateTime::RFC2822,
                \DateTime::RFC3339,
                \DateTime::RFC3339_EXTENDED,
                \DateTime::RFC7231,
                \DateTime::RSS,
                \DateTime::W3C,
            ];
        }
        else {
            $position = 0;
            foreach ($allowedFormats as $allowedFormat) {
                if (is_string($allowedFormat) !== true) {
                    throw InvalidDatetimeFormatException::stringRequired($position);
                }
                $position += 1;
            }
        }

        $this->allowedFormats = $allowedFormats;
    }


    public function process(
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {
        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
        }

        $value = $dataLocator->getCurrentValue();

        if (is_array($value) === true) {
            return ValidationResult::errorResult($dataLocator, Messages::ERROR_DATETIME_MUST_START_AS_STRING);
        }

        if (is_scalar($value) !== true) {
            return ValidationResult::errorResult(
                $dataLocator,
                Messages::ERROR_DATETIME_MUST_START_AS_STRING,
            );
        }

        // TODO - reject bools/ints?
        // TODO - needs string input
        $value = (string)$dataLocator->getCurrentValue();

        foreach ($this->allowedFormats as $allowedFormat) {
            $dateTime = \DateTimeImmutable::createFromFormat($allowedFormat, $value);

            if ($dateTime instanceof \DateTimeImmutable) {
                return ValidationResult::valueResult($dateTime);
            }
        }

        return ValidationResult::errorResult($dataLocator, Messages::ERROR_INVALID_DATETIME);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setRequired(true);
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setFormat(ParamDescription::FORMAT_DATETIME);
    }
}
