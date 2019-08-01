<?php

declare(strict_types = 1);

namespace Params\FirstRule;

use Params\SafeAccess;
use Params\SubsequentRule\SubsequentRule;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Params;
use Params\ValidationErrors;
use Params\ParamsValidator;
use Params\SubsequentRule\IntegerInput;
use Params\ParamValues;
use Params\Functions;

class GetArrayOfInt implements FirstRule
{
    /** @var SubsequentRule[] */
    private $subsequentRules;

    const ERROR_MESSAGE_NOT_SET = "Value not set for '%s'.";

    const ERROR_MESSAGE_NOT_ARRAY = "Value set for '%s' must be an array.";

    const ERROR_MESSAGE_ITEM_NOT_ARRAY = "Error for '%s'. Values for type '%s' must be an array, but got '%s'. Use GetArrayOfInt|String for single values.";

    public function __construct(SubsequentRule ...$rules)
    {
        $this->subsequentRules = $rules;
    }

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ): ValidationResult {

        if ($varMap->has($name) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_SET, $name);
            return ValidationResult::errorResult($name, $message);
        }

        $itemData = $varMap->get($name);
        if (is_array($itemData) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_ARRAY, $name);
            return ValidationResult::errorResult($name, $message);
        }

        $items = [];
        /** @var string[] $errorsMessages */
        $errorsMessages = [];
        $index = 0;

        $intRule = new IntegerInput();

        foreach ($itemData as $itemDatum) {
            $result = $intRule->process((string)$index, $itemDatum, $validator);
            $problems = $result->getProblemMessages();
            if (count($problems) !== 0) {
                $errorsMessages = Functions::addChildErrorMessagesForArray(
                    $name,
                    $problems,
                    $errorsMessages
                );
                continue;
            }

            // Is this needed? Can a FirstRule be final?
            // Maybe but kind of useless.
            if ($result->isFinalResult() === true) {
                $items[] = $result->getValue();
                continue;
            }

            $validator2 = new ParamsValidator();

            [$value, $validationErrors] =  $validator2->validateSubsequentRules(
                $result->getValue(),
                (string)$index,
                ...$this->subsequentRules
            );

            if (count($validationErrors) !== 0) {
                foreach ($validationErrors as $key => $error) {
                    $errorsMessages['/' . $name . $key] = $error;
                }
            }

            $index += 1;
            $items[] = $value;
        }

        if (count($errorsMessages) !== 0) {
            return ValidationResult::errorsResult($errorsMessages);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // TODO - implement
    }
}
