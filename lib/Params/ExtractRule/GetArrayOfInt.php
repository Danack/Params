<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use Params\ProcessRule\ProcessRule;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsValuesImpl;
use Params\ProcessRule\IntegerInput;
use Params\ParamValues;
use Params\Functions;


class GetArrayOfInt implements ExtractRule
{
    /** @var ProcessRule[] */
    private array $subsequentRules;

    const ERROR_MESSAGE_NOT_SET = "Value not set for '%s'.";

    const ERROR_MESSAGE_NOT_ARRAY = "Value set for '%s' must be an array.";

    const ERROR_MESSAGE_ITEM_NOT_ARRAY = "Error for '%s'. Values for type '%s' must be an array, but got '%s'. Use GetArrayOfInt|String for single values.";

    public function __construct(ProcessRule ...$rules)
    {
        $this->subsequentRules = $rules;
    }

    public function process(
        string $identifier,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {

        // Check its set
        if ($varMap->has($identifier) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_SET, $identifier);
            return ValidationResult::errorResult($identifier, $message);
        }

        // Check its an array
        $itemData = $varMap->get($identifier);
        if (is_array($itemData) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_ARRAY, $identifier);
            return ValidationResult::errorResult($identifier, $message);
        }

        // Setup stuff
        $items = [];
        /** @var \Params\ValidationProblem[] $validationProblems */
        $validationProblems = [];
        $index = 0;

        $intRule = new IntegerInput();

        foreach ($itemData as $itemDatum) {
            $result = $intRule->process((string)$index, $itemDatum, $paramValues);

            // If error, add it and attempt next entry in array
            if ($result->anyErrorsFound()) {
                $validationProblems = [...$validationProblems, ...$result->getValidationProblems()];

//                $validationProblems = Functions::addChildErrorMessagesForArray(
//                    $identifier,
//                    $result->getValidationProblems(),
//                    $validationProblems
//                );
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

            $identifier = (string)$index;

            $newValidationProblems =  $validator2->validateSubsequentRules(
                $result->getValue(),
                $identifier,
                ...$this->subsequentRules
            );

            $validationProblems = [...$validationProblems, ...$newValidationProblems];

            if (count($newValidationProblems) !== 0) {
                continue;
//                foreach ($validationProblems as $key => $error) {
//                    $validationProblems['/' . $identifier . $key] = $error;
//                }
            }

            if ($validator2->hasParam((string)$index) !== true) {
                throw new \Exception("Code is borked.");
            }


            $index += 1;
            $items[] = $validator2->getParam($identifier);
        }

        if (count($validationProblems) !== 0) {
            return ValidationResult::thisIsMultipleErrorResult($validationProblems);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
