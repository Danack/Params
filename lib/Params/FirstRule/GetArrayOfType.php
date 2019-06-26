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

class GetArrayOfType implements FirstRule
{
    /** @var string  */
    private $className;

    const ERROR_MESSAGE_NOT_SET = "Value not set for '%s'.";

    const ERROR_MESSAGE_NOT_ARRAY = "Value set for '%s' must be an array.";

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function process(
        string $variableName,
        VarMap $varMap,
        ParamsValidator $validator
    ): ValidationResult {
        if ($varMap->has($variableName) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_SET, $variableName);
            return ValidationResult::errorResult($message);
        }

        $itemData = $varMap->get($variableName);

        if (is_array($itemData) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_ARRAY, $variableName);
            return ValidationResult::errorResult($message);
        }

        $items = [];
        $errorsMessages = [];
        $index = 0;

        foreach ($itemData as $itemDatum) {
            $dataVarMap = new ArrayVarMap($itemDatum);
            $rules = call_user_func([$this->className, 'getRules'], $dataVarMap);

            [$item, $error] = Params::createOrError($this->className, $rules, $dataVarMap);

            if ($error !== null) {
                /** @var ValidationErrors $error */
                foreach ($error->getValidationProblems() as $validationProblem) {
                    $errorsMessages[] = '[' . $index . '] ' . $validationProblem;
                }
            }

            $index += 1;

            $items[] = $item;
        }

        if (count($errorsMessages) !== 0) {
            $errorStr = 'Error';
            if (count($errorsMessages) > 1) {
                $errorStr = 'Errors';
            }

            $message = sprintf(
                "%s in %s. %s",
                $errorStr,
                $variableName,
                implode('. ', $errorsMessages)
            );

            return ValidationResult::errorResult($message);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription)
    {
        // TODO - implement
    }
}
