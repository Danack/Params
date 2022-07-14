<?php

declare(strict_types=1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ValidationResult;
use function TypeSpec\createObjectFromProcessedValues;
use function TypeSpec\getInputTypeSpecListForClass;
use function TypeSpec\processInputTypeSpecList;

class GetType implements ExtractPropertyRule
{
    /** @var class-string */
    private string $className;

    /** @var \TypeSpec\InputTypeSpec[] */
    private array $inputParameterList;

    /**
     * @param class-string $className
     * @param \TypeSpec\InputTypeSpec[] $inputParameterList
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
            getInputTypeSpecListForClass($classname)
        );
    }


    /**
     * @param class-string $className
     * @param \TypeSpec\InputTypeSpec[] $inputParameterList
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
        $validationProblems = processInputTypeSpecList(
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
