<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use function Params\createObjectFromParams;
use function Params\getInputParameterListForClass;
use function Params\processInputParameters;

class GetType implements ExtractRule
{
    /** @var class-string */
    private string $className;

    /** @var \Params\InputParameter[] */
    private array $inputParameterList;

    /**
     * @param class-string $className
     * @param \Params\InputParameter[] $inputParameterList
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
            getInputParameterListForClass($classname)
        );
    }


    /**
     * @param class-string $className
     * @param \Params\InputParameter[] $inputParameterList
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

        $item = createObjectFromParams($this->className, $paramsValuesImpl->getAllValues());

        return ValidationResult::valueResult($item);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - how to implement this.
        $paramDescription->setRequired(true);
    }
}
