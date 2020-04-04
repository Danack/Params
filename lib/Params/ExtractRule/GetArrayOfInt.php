<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\Messages;
use Params\ProcessRule\ProcessRule;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ProcessRule\IntegerInput;
use Params\ParamValues;
use Params\Path;
use Params\DataLocator\DataLocator;

class GetArrayOfInt implements ExtractRule
{
    /** @var ProcessRule[] */
    private array $subsequentRules;

    public function __construct(ProcessRule ...$rules)
    {
        $this->subsequentRules = $rules;
    }

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues,
        DataLocator $dataLocator
    ): ValidationResult {

        // Check its set
//        if ($varMap->has($path->getCurrentName()) !== true) {
//            $message = sprintf(Messages::ERROR_MESSAGE_NOT_SET, $path->getCurrentName());
//            return ValidationResult::errorResult($dataLocator, $message);
//        }

        $itemData = $dataLocator->getCurrentValue();

        // Check its an array
//        $itemData = $varMap->get($path->getCurrentName());
        if (is_array($itemData) !== true) {
            $message = sprintf(Messages::ERROR_MESSAGE_NOT_ARRAY, $path->getCurrentName());
            return ValidationResult::errorResult($dataLocator, $message);
        }

        // Setup stuff
        $items = [];
        /** @var \Params\ValidationProblem[] $validationProblems */
        $validationProblems = [];
        $index = 0;

        $intRule = new IntegerInput();

        foreach ($itemData as $itemDatum) {
            // Create the new path.
            $pathForItem = $path->addArrayIndexPathFragment($index);

            $dataLocatorForItem = $dataLocator->moveIndex($index);

            // Process the int rule for the item
            $result = $intRule->process($pathForItem, $itemDatum, $paramValues, $dataLocatorForItem);

            // If error, add it and attempt next entry in array
            if ($result->anyErrorsFound()) {
                $validationProblems = [...$validationProblems, ...$result->getValidationProblems()];
                continue;
            }

//            // Is this needed? Can a FirstRule be final?
//            // Maybe but kind of useless.
//            TODO - add this back with a test
//            if ($result->isFinalResult() === true) {
//                $items[] = $result->getValue();
//                continue;
//            }

            $validator2 = new ParamsValuesImpl();
            $newValidationProblems =  $validator2->validateSubsequentRules(
                $result->getValue(),
                $pathForItem,
                $dataLocatorForItem,
                ...$this->subsequentRules
            );

            $validationProblems = [...$validationProblems, ...$newValidationProblems];

            if (count($newValidationProblems) !== 0) {
                continue;
            }

            $items[] = $validator2->getParam($index);
            $index += 1;
        }

        if (count($validationProblems) !== 0) {
            return ValidationResult::fromValidationProblems($validationProblems);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
