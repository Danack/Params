<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\CastToInt;
use Params\ValidationResult;

class GetInt implements ExtractRule
{
    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $intRule = new CastToInt();
        $value = $dataStorage->getCurrentValue();
        return $intRule->process($value, $processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
