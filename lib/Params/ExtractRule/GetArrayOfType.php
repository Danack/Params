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

        // Check its an array
//        $itemData = $varMap->get($path->getCurrentName());
        if (is_array($itemData) !== true) {
            return ValidationResult::errorResult($dataLocator, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
        }

        // Setup stuff
        $items = [];

        /** @var \Params\ValidationProblem[] $allValidationProblems */
        $allValidationProblems = [];
        $index = 0;

        // TODO - why don't we use the key here?
        foreach ($itemData as $itemDatum) {
//            $pathForItem = $path->addArrayIndexPathFragment($index);

            $dataLocatorForItem = $dataLocator->moveKey($index);

//            if (is_array($itemDatum) !== true) {
//                $message = sprintf(
//                    Messages::ERROR_MESSAGE_ITEM_NOT_ARRAY,
//                    $classname,
//                    gettype($itemDatum)
//                );
//
//                return ValidationResult::errorResult($path, $message);
//            }
//
//            $dataVarMap = new ArrayVarMap($itemDatum);
////
//            [$item, $validationProblems] = createOrErrorFromPath(
//                $classname,
//                $inputParameterList,
//                $dataVarMap,
//                $pathForItem
//            );

            // This appears to be wrong - why would
            $paramsValuesImpl = new ProcessedValuesImpl();

            $result = $this->typeExtractor->process(
                $paramsValuesImpl,
                $dataLocatorForItem
            );

            if ($result->anyErrorsFound() === true) {
                $allValidationProblems = [...$allValidationProblems, ...$result->getValidationProblems()];
            }
            else {
                $items[$index] = $result->getValue();
            }

            $index += 1;
        }

        if (count($allValidationProblems) !== 0) {
            return ValidationResult::fromValidationProblems($allValidationProblems);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
