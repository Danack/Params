<?php

declare(strict_types=1);

namespace Params\ExtractRule;

use Params\DataLocator\InputStorageAye;
use Params\Messages;
use Params\ProcessedValuesImpl;
use Params\ProcessRule\IntegerInput;
use Params\ValidationResult;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\Path;
use function Params\createObjectFromParams;
use function Params\createOrErrorFromPath;
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
     * @param class-string $className
     */
    public static function fromClass(string $className)
    {
        return new self(
            $className,
            getInputParameterListForClass($className)
        );
    }


    /**
     * @param class-string $className
     * @param \Params\InputParameter[] $inputParameterList
     */
    public static function fromClassAndRules(string $className, $inputParameterList)
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
//        if ($varMap->has($path->getCurrentName()) !== true) {
//            return ValidationResult::errorResult($path, Messages::VALUE_NOT_SET);
//        }
//
//        // Check its an array - why is this required? We should support both array and
//        // single value types...
//        $itemData = $varMap->get($path->getCurrentName());
//        if (is_array($itemData) !== true) {
//            return ValidationResult::errorResult($path, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
//        }

        $paramsValuesImpl = new ProcessedValuesImpl();
        [$validationProblems, $value] = processInputParameters(
            $this->inputParameterList,
            $paramsValuesImpl,
            $dataLocator
        );

        if (count($validationProblems) !== 0) {
            return ValidationResult::fromValidationProblems($validationProblems);
        }

        $item = createObjectFromParams($this->className, $paramsValuesImpl->getAllValues());

//        return [$object, []];

//        [$item, $validationProblems] = createOrErrorFromPath(
//            $this->className,
//            $this->inputParameterList,
//            $varMap,
//            $path
//        );

//        if (count($validationProblems) !== 0) {
//
//        }

        return ValidationResult::valueResult($item);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        $paramDescription->setType(ParamDescription::TYPE_INTEGER);
        $paramDescription->setRequired(true);
    }
}
