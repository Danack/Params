<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\FloatInput;
use Params\ValidationResult;

class GetFloat implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ) : ValidationResult {
        // TODO - this is an error. It should be getCurrentName
        // Fix this after writing a test to detect this type of issue.
        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
        }

        $intRule = new FloatInput();

        return $intRule->process(
            $dataLocator->getCurrentValue(),
            $processedValues,
            $dataLocator
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_NUMBER);
        $paramDescription->setRequired(true);
    }
}
