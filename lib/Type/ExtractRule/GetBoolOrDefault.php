<?php

declare(strict_types=1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ProcessRule\CastToBool;
use Type\ValidationResult;

/**
 * If a parameter is not set, then the value is the default value.
 */
class GetBoolOrDefault implements ExtractPropertyRule
{
    private ?bool $default;

    /**
     * setOrDefaultValidator constructor.
     * @param bool $default The default value to use.
     */
    public function __construct(?bool $default)
    {
        $this->default = $default;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new CastToBool();

        return $intRule->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
