<?php

declare(strict_types = 1);

namespace Params\Rule;

use Params\SafeAccess;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\Rule;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\Params;
use Params\ValidationErrors;

class GetArrayOfType implements Rule
{
    /** @var VarMap */
    protected $variableMap;

    /** @var string  */
    private $className;

    const ERROR_MESSAGE_NOT_SET = "Value not set for '%s'.";

    const ERROR_MESSAGE_NOT_ARRAY = "Value set for '%s' must be an array.";

    public function __construct(VarMap $variableMap, string $className)
    {
        $this->variableMap = $variableMap;
        $this->className = $className;
    }

    public function __invoke(string $name, $_): ValidationResult
    {
        if ($this->variableMap->has($name) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_SET, $name);
            return ValidationResult::errorResult($message);
        }

        $itemData = $this->variableMap->get($name);

        if (is_array($itemData) !== true) {
            $message = sprintf(self::ERROR_MESSAGE_NOT_ARRAY, $name);
            return ValidationResult::errorResult($message);
        }

        $items = [];
        $errorsMessages = [];
        $index = 0;

        foreach ($itemData as $itemDatum) {
            $dataVarMap = new ArrayVarMap($itemDatum);
            $rules = call_user_func([$this->className, 'getRules'], $dataVarMap);

            [$item, $error] = Params::createOrError($this->className, $rules);

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
                $name,
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
