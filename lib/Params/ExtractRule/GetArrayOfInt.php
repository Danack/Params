<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\InputStorage\InputStorage;
use Params\Messages;
use Params\OpenApi\ParamDescription;
use Params\ProcessedValues;
use Params\ProcessRule\IntegerInput;
use Params\ProcessRule\ProcessRule;
use Params\ValidationResult;
use function Params\processProcessingRules;

class GetArrayOfInt implements ExtractRule
{
    /** @var ProcessRule[] */
    private array $subsequentRules;

    public function __construct(ProcessRule ...$rules)
    {
        $this->subsequentRules = $rules;
    }

    public function process(
        ProcessedValues $processedValues,
        InputStorage $dataLocator
    ): ValidationResult {

        // Check its set
        if ($dataLocator->isValueAvailable() !== true) {
            $message = sprintf(Messages::ERROR_MESSAGE_NOT_SET);
            return ValidationResult::errorResult($dataLocator, $message);
        }

        $itemData = $dataLocator->getCurrentValue();

        // Check its an array
        if (is_array($itemData) !== true) {
            $message = sprintf(Messages::ERROR_MESSAGE_NOT_ARRAY);
            return ValidationResult::errorResult($dataLocator, $message);
        }

        // Setup stuff
        $items = [];
        /** @var \Params\ValidationProblem[] $validationProblems */
        $validationProblems = [];
        $index = 0;

        $intRule = new IntegerInput();

        foreach ($itemData as $itemDatum) {
            $dataLocatorForItem = $dataLocator->moveKey($index);

            // Process the int rule for the item
            $result = $intRule->process($itemDatum, $processedValues, $dataLocatorForItem);

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
                $dataLocatorForItem,
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
