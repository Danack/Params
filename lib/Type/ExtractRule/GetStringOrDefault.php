<?php

declare(strict_types=1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;

class GetStringOrDefault implements ExtractPropertyRule
{
    private ?string $default;

    /**
     * setOrDefaultValidator constructor.
     * @param ?string $default
     */
    public function __construct(?string $default)
    {
        $this->default = $default;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $value = (string)$dataStorage->getCurrentValue();

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
