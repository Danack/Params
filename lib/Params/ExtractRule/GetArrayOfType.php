<?php

declare(strict_types = 1);

namespace Params\ExtractRule;

use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\ValidationResult;
use Params\OpenApi\ParamDescription;
use Params\ParamsExecutor;
use Params\ParamValues;
use Params\Path;

class GetArrayOfType implements ExtractRule
{
    /** @var class-string<mixed> */
    private string $className;

    const ERROR_MESSAGE_NOT_SET = "Value must be set.";

    const ERROR_MESSAGE_NOT_ARRAY = "Value must be an array.";

    const ERROR_MESSAGE_ITEM_NOT_ARRAY = "Values for type '%s' must be an array, but got '%s'. Use GetArrayOfInt|String for single values.";

    /**
     * @param class-string<mixed> $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function process(
        Path $path,
        VarMap $varMap,
        ParamValues $paramValues
    ): ValidationResult {

        // Check its set
        if ($varMap->has($path->getCurrentName()) !== true) {
            return ValidationResult::errorResult($path->toString(), self::ERROR_MESSAGE_NOT_SET);
        }

        // Check its an array
        $itemData = $varMap->get($path->getCurrentName());
        if (is_array($itemData) !== true) {
            return ValidationResult::errorResult($path->toString(), self::ERROR_MESSAGE_NOT_ARRAY);
        }

        // Setup stuff
        $items = [];
        /** @var array<string> $allValidationProblems */
        $allValidationProblems = [];
        $index = 0;

        foreach ($itemData as $itemDatum) {
            if (is_array($itemDatum) !== true) {
                $message = sprintf(
                    self::ERROR_MESSAGE_ITEM_NOT_ARRAY,
                    $this->className,
                    gettype($itemDatum)
                );

                return ValidationResult::errorResult($path->toString(), $message);
            }

            $dataVarMap = new ArrayVarMap($itemDatum);
            $rules = call_user_func([$this->className, 'getInputToParamInfoList'], $dataVarMap);

            [$item, $validationProblems] = ParamsExecutor::createOrError($this->className, $rules, $dataVarMap);
            $allValidationProblems = [...$allValidationProblems, ...$validationProblems];

            $index += 1;
            $items[] = $item;
        }

        if (count($allValidationProblems) !== 0) {
            return ValidationResult::thisIsMultipleErrorResult($allValidationProblems);
        }

        return ValidationResult::valueResult($items);
    }

    public function updateParamDescription(ParamDescription $paramDescription): void
    {
        // TODO - implement
    }
}