<?php

declare(strict_types = 1);

namespace Params\FirstRule;

use Params\SafeAccess;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Params;
use Params\ValidationErrors;
use Params\ParamsValidator;
use Params\ParamValues;

class GetArrayOfType implements FirstRule
{
    /** @var string  */
    private $className;

    const ERROR_MESSAGE_NOT_SET = "Value not set for '%s'.";

    const ERROR_MESSAGE_NOT_ARRAY = "Value set for '%s' must be an array.";

    const ERROR_MESSAGE_ITEM_NOT_ARRAY = "Error for '%s'. Values for type '%s' must be an array, but got '%s'. Use GetArrayOfInt|String for single values.";

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function process(
        string $name,
        VarMap $varMap,
        ParamValues $validator
    ): ValidationResult {
        if ($varMap->has($name) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_SET, $name);
            return ValidationResult::errorResult($message);
        }

        $itemData = $varMap->get($name);

        if (is_array($itemData) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_ARRAY, $name);
            return ValidationResult::errorResult($message);
        }

        $items = [];
        $errorsMessages = [];
        $index = 0;

        foreach ($itemData as $itemDatum) {
            if (is_array($itemDatum) !== true) {
                $message = sprintf(
                    self::ERROR_MESSAGE_ITEM_NOT_ARRAY,
                    $name,
                    $this->className,
                    gettype($itemDatum)
                );

                return ValidationResult::errorResult($message);
            }

            $dataVarMap = new ArrayVarMap($itemDatum);
            $rules = call_user_func([$this->className, 'getRules'], $dataVarMap);

            [$item, $errors] = Params::createOrError($this->className, $rules, $dataVarMap);

            if ($errors !== null) {
                foreach ($errors as $error) {
                    $errorsMessages[] = 'Error [' . $index . '] ' . $error;
                }
            }

            $index += 1;

            $items[] = $item;
        }

        if (count($errorsMessages) !== 0) {
//            $errorStr = 'Error';
//            if (count($errorsMessages) > 1) {
//                $errorStr = 'Errors';
//            }
//
//            $message = sprintf(
//                "%s in %s. %s",
//                $errorStr,
//                $name,
//                implode('. ', $errorsMessages)
//            );

            return ValidationResult::errorsResult($errorsMessages);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // TODO - implement
    }
}
