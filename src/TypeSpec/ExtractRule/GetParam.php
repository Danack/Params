<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\PropertyInputTypeSpec;
use TypeSpec\ValidationResult;
use function TypeSpec\createObjectFromProcessedValues;
use function TypeSpec\processSingleInputParameter;

class GetParam implements ExtractPropertyRule
{
    /** @var class-string */
    private string $className;

    private PropertyInputTypeSpec $propertyInputTypeSpec;

    /**
     * @param class-string $className
     * @param PropertyInputTypeSpec $propertyInputTypeSpec
     */
    public function __construct(string $className, PropertyInputTypeSpec $propertyInputTypeSpec)
    {
        $this->className = $className;
        $this->propertyInputTypeSpec = $propertyInputTypeSpec;
    }

    /**
     * @param class-string $classname
     */
    public static function fromClass(string $classname): self
    {
        throw new \Exception("This code appears deadish. Is this covered?");
    }


    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $paramsValuesImpl = new ProcessedValues();
        $validationProblems = processSingleInputParameter(
            $this->propertyInputTypeSpec,
            $paramsValuesImpl,
            $dataStorage
        );

        if (count($validationProblems) !== 0) {
            return ValidationResult::fromValidationProblems($validationProblems);
        }

        $item = createObjectFromProcessedValues($this->className, $paramsValuesImpl);

        return ValidationResult::valueResult($item);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - how to implement this.
        $paramDescription->setRequired(true);
    }
}
