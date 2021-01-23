<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;

/**
 * This is a useless clas
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
        InputStorage $dataLocator
    ): ValidationResult {
        if ($dataLocator->isValueAvailable() !== true) {
            return ValidationResult::valueResult(null);
        }

        return parent::process($processedValues, $dataLocator);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        parent::updateParamDescription($paramDescription);
        $paramDescription->setRequired(false);
    }
}
