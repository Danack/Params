<?php

namespace Params;

use Params\DataLocator\StandardDataLocator;
use Params\Exception\InputParameterListException;
use Params\Exception\LogicException;
use Params\Exception\MissingClassException;
use Params\Exception\PatchFormatException;
use Params\Exception\TypeNotInputParameterListException;
use Params\Exception\ValidationException;
use Params\ExtractRule\GetArrayOfType;
use Params\PatchOperation\PatchOperation;
use Params\Value\Ordering;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\DataLocator\DataLocator;

/**
 * @param mixed $value
 * @return string
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
                Messages::ERROR_MESSAGE_ITEM_NOT_ARRAY,
                $classname,
                gettype($itemDatum)
            );

            return ValidationResult::errorResult($path, $message);
        }

        $dataVarMap = new ArrayVarMap($itemDatum);

        [$item, $validationProblems] = createOrErrorFromPath(
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
        return ValidationResult::fromValidationProblems($allValidationProblems);
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

    // Validate all entries are InputParameters
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


/**
 * @template T
 * @param class-string<T> $classname
 * @param array $values
 * @return T of object
 * @throws \ReflectionException
 */
function createObjectFromParams($classname, $values)
{
    $reflection_class = new \ReflectionClass($classname);
    $object = $reflection_class->newInstanceArgs($values);

    /** @var T $object */
    return $object;
}


/**
 * @param \Params\PatchRule\PatchRule[] $patchRules
 * @param array $sourceData
 * @return array
 */
function processOperations(
    $patchRules,
    array $sourceData
) {
    $validationResult = PatchFactory::convertInputToPatchObjects($sourceData);

    if ($validationResult->anyErrorsFound()) {
        throw new PatchFormatException(
            "Patch format error: " . implode(",", $validationResult->getPatchObjectProblems())
        );
    }

    $patchOperations = $validationResult->getPatchOperations();

    $operationObjects = [];
    $allProblems = [];

    foreach ($patchOperations as $patchOperation) {
        [$operationObject, $problems] = applyRulesToPatchOperation($patchOperation, $patchRules);

        if ($operationObject !== null) {
            $operationObjects[] = $operationObject;
        }
        if (count($problems) !== 0) {
            array_push($allProblems, ...$problems);
        }
    }

    if (count($allProblems) !== 0) {
        return [null, $allProblems];
    }

    return [$operationObjects, []];
}


/**
 * @param string $path
 * @param string $pathRegex
 * @return array{0: bool, 1: string}
 */
function pathMatches(string $path, string $pathRegex)
{
    // Putting it mildly, this does not look correct.
    // TODO - we need to return bool $isMatch, and array $namedParams
    return [true, $path];
}




/**
 * @param PatchOperation $patchObject
 * @param \Params\PatchRule\PatchRule[] $patchRules
 * @return array
 * @throws Exception\ParamsException
 * @throws ValidationException
 */
function applyRulesToPatchOperation(
    PatchOperation $patchObject,
    $patchRules
) {
    foreach ($patchRules as $patchRule) {

        /** @var \Params\PatchRule\PatchRule $patchRule */
        if ($patchObject->getOpType() !== $patchRule->getOpType()) {
            continue;
        }

        [$pathMatch, $params] = pathMatches(
            $patchObject->getPath(),
            $patchRule->getPathRegex()
        );

        if ($pathMatch !== true) {
            continue;
        }

        // TODO - pass in $params here also
        return createOrError(
            $patchRule->getClassName(),
            $patchRule->getRules(),
            new ArrayVarMap($patchObject->getValue())
        );
    }

    $message = sprintf(
        "Failed to match path '%s' for op type '%s'",
        $patchObject->getPath(),
        $patchObject->getOpType()
    );

    // TODO - This is a bug. Message shouldn't be a string but a ValidationProblem
    return [null, [$message]];
}


/**
 * Creating patches is slightly harder than creating parameters. For Params the order of parameters isn't
 * important, for patch operations it is. e.g. copy -> delete != delete->copy
 *
 * @param \Params\PatchRule\PatchRule[] $namedRules
 * @param array $sourceData
 * @return array
 * @throws PatchFormatException
 * @throws ValidationException
 */
function createPatch($namedRules, array $sourceData): array
{
    [$operations, $validationProblems] = processOperations($namedRules, $sourceData);

    if (count($validationProblems) !== 0) {
        throw new ValidationException(
            "Validation problems",
            $validationProblems
        );
    }

    return $operations;
}

/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\Param[] $params
 * @param Path $path
 * @return array{0:?object, 1:\Params\ValidationProblem[]}
 * @throws Exception\ParamsException
 * @throws ValidationException
 *
 * The rules are passed separately to the classname so that we can
 * support rules coming both from static info and from factory objects.
 */
function createOrErrorFromPath($classname, $params, VarMap $sourceData, Path $path)
{
    $paramsValuesImpl = new ParamsValuesImpl();
    $validationProblems = $paramsValuesImpl->executeRulesWithValidator($params, $sourceData, $path);

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $object = createObjectFromParams($classname, $paramsValuesImpl->getParamsValues());

    return [$object, []];
}


/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\Param[] $params
 * @param VarMap $sourceData
 * @return T of object
 * @throws ValidationException
 * @throws \ReflectionException
 */
function create($classname, $params, VarMap $sourceData)
{
    $paramsValuesImpl = new ParamsValuesImpl();
    $path = Path::initial();
    $dataLocator = StandardDataLocator::fromVarMap($sourceData);

    $validationProblems = $paramsValuesImpl->executeRulesWithValidator(
        $params,
        $sourceData,
        $path,
        $dataLocator
    );

    if (count($validationProblems) !== 0) {
        throw new ValidationException("Validation problems", $validationProblems);
    }
    $object = createObjectFromParams($classname, $paramsValuesImpl->getParamsValues());

    /** @var T $object */
    return $object;
}


/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\Param[] $params
 * @return array{0:?object, 1:\Params\ValidationProblem[]}
 * @throws Exception\ParamsException
 * @throws ValidationException
 *
 * The rules are passed separately to the classname so that we can
 * support rules coming both from static info and from factory objects.
 */
function createOrError($classname, $params, VarMap $sourceData)
{
    $paramsValuesImpl = new ParamsValuesImpl();
    $path = Path::initial();
    $dataLocator = StandardDataLocator::fromVarMap($sourceData);

    $validationProblems = $paramsValuesImpl->executeRulesWithValidator(
        $params,
        $sourceData,
        $path,
        $dataLocator
    );

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $object = createObjectFromParams($classname, $paramsValuesImpl->getParamsValues());

    // TODO - wrap this in an ResultObject.
    return [$object, []];
}


/**
 * Unescapes a json pointer part
 *
 * https://tools.ietf.org/html/rfc6901#section-4
 *
 * @param string $pointer
 */
function escapeJsonPointer(string $pointer): string
{
    // then transforming any occurrence of the sequence '~0' to '~'
    $result = str_replace('~', '~0', $pointer);
    // first transforming any occurrence of the sequence '~1' to '/'
    $result = str_replace('/', '~1', $result);

    return $result;
}

/**
 * Unescapes a json pointer part
 *
 * https://tools.ietf.org/html/rfc6901#section-4
 *
 * @param string $pointer
 */
function unescapeJsonPointer(string $pointer): string
{
    // first transforming any occurrence of the sequence '~1' to '/'
    $result = str_replace('~1', '/', $pointer);

    // then transforming any occurrence of the sequence '~0' to '~'

    $result = str_replace('~0', '~', $result);

    return $result;
}



/**
 * @param array<mixed> $array
 * @param mixed $value
 * @return bool
 */
function array_value_exists(array $array, $value): bool
{
    return in_array($value, $array, true);
}


/**
 * @param string $name string The name of the variable
 * @param mixed $value  The value of the variable
 * @return null|string returns an error string, when there is an error
 */
function check_only_digits(string $name, $value)
{
    if (is_int($value) === true) {
        return null;
    }

    $count = preg_match("/[^0-9]+/", $value, $matches, PREG_OFFSET_CAPTURE);

    if ($count === false) {
        throw new LogicException("preg_match failed");
    }

    if ($count !== 0) {
        $badCharPosition = $matches[0][1];
        $message = sprintf(
            "Value for '$name' must contain only digits. Non-digit found at position %d.",
            $badCharPosition
        );
        return $message;
    }

    return null;
}

/**
 * Separates an order parameter such as "+name", into the 'name' and
 * 'ordering' parts.
 * @param string $part
 * @return array{string, string}
 */
function normalise_order_parameter(string $part)
{
    if (substr($part, 0, 1) === "+") {
        return [substr($part, 1), Ordering::ASC];
    }

    if (substr($part, 0, 1) === "-") {
        return [substr($part, 1), Ordering::DESC];
    }

    return [$part, Ordering::ASC];
}



function createPath(array $pathParts)
{
    $path = '';

    if (count($pathParts) === 0) {
        return '/';
    }

    foreach ($pathParts as $type => $value) {
        if ($type === 'index') {
            $path .= '/[' . $value . ']';
        }
        else if ($type === 'name') {
            $path .= '/' . $value;
        }
        else {
            throw new \LogicException("Unknown type " . $type);
        }
    }

    return $path;
}

