<?php

declare(strict_types=1);

namespace Type\ExtractRule;

use Type\DataStorage\DataStorage;
use Type\Messages;
use Type\OpenApi\ParamDescription;
use Type\ProcessedValues;
use Type\ValidationResult;
use function Type\createObjectFromProcessedValues;
use function Type\getPropertyDefinitionsForClass;
use function Type\processInputParameters;

class GetType implements ExtractPropertyRule
{
    /** @var class-string */
    private string $className;

    /** @var \Type\PropertyDefinition[] */
    private array $inputParameterList;

    /**
     * @param class-string $className
     * @param \Type\PropertyDefinition[] $inputParameterList
     */
    public function __construct(string $className, $inputParameterList)
    {
        $this->className = $className;
        $this->inputParameterList = $inputParameterList;
    }

    /**
     * @param class-string $classname
     */
    public static function fromClass(string $classname): self
    {
        return new self(
            $classname,
            getPropertyDefinitionsForClass($classname)
        );
    }


    /**
     * @param class-string $className
     * @param \Type\PropertyDefinition[] $inputParameterList
     */
    public static function fromClassAndRules(string $className, $inputParameterList): self
    {
        return new self(
            $className,
            $inputParameterList
        );
    }


    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $paramsValuesImpl = new ProcessedValues();
        $validationProblems = processInputParameters(
            $this->inputParameterList,
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
