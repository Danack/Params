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
use Params\Path;

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
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {

        // Check its set
        if ($varMap->has($path->getCurrentName()) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_SET, $path->getCurrentName());
            return ValidationResult::errorResult($path, $message);
        }

        // Check its an array
        $itemData = $varMap->get($path->getCurrentName());
        if (is_array($itemData) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_ARRAY, $path->getCurrentName());
            return ValidationResult::errorResult($path, $message);
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

            // Process the int rule for the item
            $result = $intRule->process($pathForItem, $itemDatum, $paramValues);

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
            return ValidationResult::thisIsMultipleErrorResult($validationProblems);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}
