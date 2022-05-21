<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataStorage\DataStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ValidationResult;
use function Params\createObjectFromProcessedValues;
use function Params\getParamForClass;
use function Params\processSingleInputParameter;
use Params\Param;

class GetParam implements ExtractRule
{
    /** @var class-string */
    private string $className;

    private Param $param;

    /**
     * @param class-string $className
     * @param Param $param
     */
    public function __construct(string $className, Param $param)
    {
        $this->className = $className;
        $this->param = $param;
    }

    /**
     * @param class-string $classname
     */
    public static function fromClass(string $classname): self
    {
        throw new \Exception("Is this covered?");

//        return new self(
//            $classname,
//            getParamForClass($classname)
//        );
    }


//    /**
//     * @param class-string $className
//     * @param \Params\InputParameter[] $inputParameterList
//     */
//    public static function fromClassAndRules(string $className, $inputParameterList): self
//    {
//        return new self(
//            $className,
//            $inputParameterList
//        );
//    }


    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ) : ValidationResult {
        if ($dataStorage->isValueAvailable() !== true) {
            return ValidationResult::errorResult($dataStorage, Messages::VALUE_NOT_SET);
        }

        $paramsValuesImpl = new ProcessedValues();
        $validationProblems = processSingleInputParameter(
            $this->param,
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
