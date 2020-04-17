<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\Messages;
use Params\InputParameter;
use Params\ProcessedValuesImpl;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\Path;
use Params\DataLocator\InputStorageAye;
use function Params\createOrErrorFromPath;
use function Params\getInputParameterListForClass;
use function Params\createArrayForTypeWithRules;
use function Params\createArrayOfTypeDja;


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

        return createArrayOfTypeDja(
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
