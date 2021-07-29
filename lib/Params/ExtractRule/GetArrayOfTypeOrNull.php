<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

/**
 * This is a useless class - edit 23rd July - why did I write that?
 * Is it because you should do arrayOfTypeOrDefault or Optional?
 */
class GetArrayOfTypeOrNull extends GetArrayOfType implements ExtractRule
{
    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        parent::__construct($className);
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return parent::process($processedValues, $dataStorage);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        parent::updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
