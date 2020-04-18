<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\BoolInput;
use Params\ValidationResult;

class GetBoolOrDefault implements ExtractRule
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
        InputStorageAye $dataLocator
    ): ValidationResult {
        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::valueResult($this->default);
        }

        $intRule = new BoolInput();

        return $intRule->process(
            $dataLocator->getCurrentValue(),
            $processedValues,
            $dataLocator
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_BOOLEAN);
        $paramDescription->setDefault($this->default);
        $paramDescription->setRequired(false);
    }
}
