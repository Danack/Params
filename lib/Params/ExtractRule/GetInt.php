<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\IntegerInput;
use Params\ValidationResult;

class GetInt implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ) : ValidationResult {
        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
        }

        $intRule = new IntegerInput();
        $value = $dataLocator->getCurrentValue();
        return $intRule->process($value, $processedValues, $dataLocator);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
