<?php

declare(strict_types = 1);

namespace TypeSpec\ExtractRule;

use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\CastToInt;
use TypeSpec\ProcessRule\ProcessPropertyRule;
use TypeSpec\ValidationResult;
use function TypeSpec\processProcessingRules;

class GetArrayOfInt implements ExtractPropertyRule
{
    /** @var ProcessPropertyRule[] */
    private array $subsequentRules;

    public function __construct(ProcessPropertyRule ...$rules)
    {
        $this->subsequentRules = $rules;
    }

    public function process(
        ProcessedValues $processedValues,
        DataStorage $dataStorage
    ): ValidationResult {

        // Check its set
        if ($dataStorage->isValueAvailable() !== true) {
            $message = sprintf(Messages::ERROR_MESSAGE_NOT_SET);
            return ValidationResult::errorResult($dataStorage, $message);
        }

        $itemData = $dataStorage->getCurrentValue();

        // Check its an array
        if (is_array($itemData) !== true) {
            $message = sprintf(Messages::ERROR_MESSAGE_NOT_ARRAY);
            return ValidationResult::errorResult($dataStorage, $message);
        }

        // Setup stuff
        $items = [];
        /** @var \TypeSpec\ValidationProblem[] $validationProblems */
        $validationProblems = [];
        $index = 0;

        $intRule = new CastToInt();

        foreach ($itemData as $itemDatum) {
            $dataStorageForItem = $dataStorage->moveKey($index);

            // Process the int rule for the item
            $result = $intRule->process($itemDatum, $processedValues, $dataStorageForItem);

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

            $validator2 = new ProcessedValues();
            [$newValidationProblems, $processedValue] = processProcessingRules(
                $result->getValue(),
                $dataStorageForItem,
                $validator2,
                ...$this->subsequentRules
            );

            $validationProblems = [...$validationProblems, ...$newValidationProblems];

            if (count($newValidationProblems) !== 0) {
                continue;
            }

            $items[] = $processedValue; // $validator2->getParam($index);
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
