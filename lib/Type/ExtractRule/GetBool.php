<?php

declare(strict_types = 1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ProcessRule\CastToBool;
use Type\ValidationResult;

class GetBool implements ExtractPropertyRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
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
        $paramDescription->setRequired(true);
    }
}
