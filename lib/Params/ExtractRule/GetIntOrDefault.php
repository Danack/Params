<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\CastToInt;
use Params\ValidationResult;

class GetIntOrDefault implements ExtractRule
{
    private ?int $default;

    /**
     * @param ?int $default
     */
    public function __construct(?int $default)
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

        $intRule = new CastToInt();

        return $intRule->process(
            $dataStorage->getCurrentValue(),
            $processedValues,
            $dataStorage
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
