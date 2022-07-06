<?php

declare(strict_types=1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ProcessRule\CastToFloat;
use Type\ValidationResult;

class GetFloatOrDefault implements ExtractPropertyRule
{
    private ?float $default;

    /**
     * @param float $default
     */
    public function __construct(?float  $default)
    {
        $this->default = $default;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {

        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $floatInput = new CastToFloat();

        return $floatInput->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
