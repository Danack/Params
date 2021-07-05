<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

class GetStringOrDefault implements ExtractRule
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
        InputStorage $dataLocator
    ): ValidationResult {
        if ($dataLocator->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $value = (string)$dataLocator->getCurrentValue();

        return ValidationResult::valueResult($value);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_STRING);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
