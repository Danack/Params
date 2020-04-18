<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use function Params\createArrayOfType;
use function Params\getInputParameterListForClass;

class GetArrayOfType implements ExtractRule
{
    /** @var class-string */
    private string $className;

    /** @var \Params\InputParameter[] */
    private array $inputParameterList;

    private GetType $typeExtractor;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
        $this->inputParameterList = getInputParameterListForClass($this->className);

        $this->typeExtractor = GetType::fromClassAndRules(
            $this->className,
            $this->inputParameterList
        );
    }

    public function process(
        ProcessedValues $processedValues,
        InputStorageAye $dataLocator
    ): ValidationResult {

        // Check it is set
        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::ERROR_MESSAGE_NOT_SET_VARIANT_1);
        }

        $itemData = $dataLocator->getCurrentValue();

        if (is_array($itemData) !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
        }

        return createArrayOfType(
            $dataLocator,
            $itemData,
            $this->typeExtractor
        );
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
