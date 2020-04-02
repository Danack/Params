<?php

namespace Params;

use Params\Exception\InputParameterListException;
use Params\Exception\MissingClassException;
use Params\Exception\TypeNotInputParameterListException;
use Params\ExtractRule\GetArrayOfType;
use VarMap\ArrayVarMap;

/**
 * @param mixed $value
 */
function getTypeForErrorMessage($value): string
{
    if (is_object($value) === true) {
        return get_class($value);
    }

    return gettype($value);
}


/**
 * @param Path $path
 * @param class-string $classname
 * @param mixed $itemData
 * @param \Params\Param[] $inputParameterList
 * @return ValidationResult
 * @throws \Params\Exception\ParamsException
 * @throws \Params\Exception\ValidationException
 */
function createArrayForTypeWithRules(Path $path, string $classname, $itemData, array $inputParameterList)
{
    // Setup stuff
    $items = [];

    /** @var \Params\ValidationProblem[] $allValidationProblems */
    $allValidationProblems = [];
    $index = 0;

    // TODO - why don't we use the key here?
    foreach ($itemData as $itemDatum) {
        $pathForItem = $path->addArrayIndexPathFragment($index);

        if (is_array($itemDatum) !== true) {
            $message = sprintf(
                GetArrayOfType::ERROR_MESSAGE_ITEM_NOT_ARRAY,
                $classname,
                gettype($itemDatum)
            );

            return ValidationResult::errorResult($path, $message);
        }

        $dataVarMap = new ArrayVarMap($itemDatum);

        [$item, $validationProblems] = ParamsExecutor::createOrErrorFromPath(
            $classname,
            $inputParameterList,
            $dataVarMap,
            $pathForItem
        );
        $allValidationProblems = [...$allValidationProblems, ...$validationProblems];

        $index += 1;

        // TODO - should this skip if there were any problems validating
        // the rules?
        $items[] = $item;
    }

    if (count($allValidationProblems) !== 0) {
        return ValidationResult::thisIsMultipleErrorResult($allValidationProblems);
    }

    return ValidationResult::valueResult($items);
}

/**
 * @param string $className
 * @return \Params\Param[]
 * @throws InputParameterListException
 * @throws MissingClassException
 * @throws TypeNotInputParameterListException
 */
function getInputParameterListForClass(string $className)
{
    if (class_exists($className) !== true) {
        throw MissingClassException::fromClassname($className);
    }

    $implementsInterface = is_subclass_of(
        $className,
        InputParameterList::class,
        $allow_string = true
    );

    if ($implementsInterface !== true) {
        throw TypeNotInputParameterListException::fromClassname($className);
    }

    // Type is okay, get data and validate
    $inputParameterList = call_user_func([$className, 'getInputParameterList']);

    if (is_array($inputParameterList) !== true) {
        throw InputParameterListException::notArray($className);
    }

    $index = 0;
    foreach ($inputParameterList as $inputParameter) {
        if (!$inputParameter instanceof Param) {
            throw InputParameterListException::notInputParameter($index, $className);
        }

        $index += 1;
    }


    // All okay, array contains only Param items.
    /** @var \Params\Param[] $inputParameterList */
    return $inputParameterList;
}
