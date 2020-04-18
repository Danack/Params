<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessedValuesImpl;
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
    protected function __construct(string $className, $inputParameterList)
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
        InputStorageAye $dataLocator
    ) : ValidationResult {
        if ($dataLocator->valueAvailable() !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::VALUE_NOT_SET);
        }

        $paramsValuesImpl = new ProcessedValuesImpl();
        $validationProblems = processInputParameters(
            $this->inputParameterList,
            $paramsValuesImpl,
            $dataLocator
        );

        if (count($validationProblems) !== 0) {
            return ValidationResult::fromValidationProblems($validationProblems);
        }

        $item = createObjectFromParams($this->className, $paramsValuesImpl->getAllValues());

        return ValidationResult::valueResult($item);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}