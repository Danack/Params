<?php

namespace Params;

use Params\DataLocator\DataStorage;
use Params\Exception\InputParameterListException;
use Params\Exception\LogicException;
use Params\Exception\MissingClassException;
use Params\Exception\PatchFormatException;
use Params\Exception\TypeNotInputParameterListException;
use Params\Exception\ValidationException;
use Params\ExtractRule\GetArrayOfType;
use Params\PatchOperation\PatchOperation;
use Params\ProcessRule\ProcessRule;
use Params\Value\Ordering;
use VarMap\ArrayVarMap;
use VarMap\VarMap;
use Params\DataLocator\InputStorageAye;
use Params\Messages;

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
 * @param \Params\InputParameter[] $inputParameterList
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

            throw new \Exception("needs fixing.");

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
 * @return \Params\InputParameter[]
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
        if (!$inputParameter instanceof InputParameter) {
            throw InputParameterListException::notInputParameter($index, $className);
        }

        $index += 1;
    }

    // All okay, array contains only Param items.
    /** @var \Params\InputParameter[] $inputParameterList */
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
 * @param \Params\InputParameter[] $params
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
    $paramsValuesImpl = new ProcessedValuesImpl();
    $dataLocator = DataStorage::fromVarMap($sourceData);

    $validationProblems = processInputParameters($params, $paramsValuesImpl, $dataLocator);

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $object = createObjectFromParams($classname, $paramsValuesImpl->getAllValues());

    return [$object, []];
}


/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\InputParameter[] $params
 * @param VarMap $sourceData
 * @return T of object
 * @throws ValidationException
 * @throws \ReflectionException
 */
function create(
    $classname,
    $params,
    VarMap $sourceData,
    DataStorage $dataLocator
) {
    $paramsValuesImpl = new ProcessedValuesImpl();
//    $path = Path::initial();
//    $dataLocator = DataStorage::fromVarMap($sourceData);

    $validationProblems = processInputParameters(
        $params,
        $paramsValuesImpl,
        $dataLocator
    );

    if (count($validationProblems) !== 0) {
        throw new ValidationException("Validation problems", $validationProblems);
    }
    $object = createObjectFromParams($classname, $paramsValuesImpl->getAllValues());

    /** @var T $object */
    return $object;
}


/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\InputParameter[] $params
 * @return array{0:?object, 1:\Params\ValidationProblem[]}
 * @throws Exception\ParamsException
 * @throws ValidationException
 *
 * The rules are passed separately to the classname so that we can
 * support rules coming both from static info and from factory objects.
 */
function createOrError($classname, $params, VarMap $sourceData)
{
    $paramsValuesImpl = new ProcessedValuesImpl();
    $path = Path::initial();
    $dataLocator = DataStorage::fromVarMap($sourceData);

    $validationProblems = processInputParameters(
        $params,
        $paramsValuesImpl,
        $dataLocator
    );

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $object = createObjectFromParams($classname, $paramsValuesImpl->getAllValues());

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

 * @param mixed $value  The value of the variable
 * @return null|string returns an error string, when there is an error
 */
function check_only_digits($value)
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
            Messages::ONLY_DIGITS_ALLOWED,
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



function createPath(array $pathParts): string
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
// *
// * @TODO - less 'yo dawg' in function name...
// *
// ProcessRule[] $subsequentRules>

/**
 * @param ProcessRule ...$processRules
 */
function blah(ProcessRule ...$processRules): void
{


}

/**
 * @return mixed
 */
function foo()
{
    return null;
}


/**
 * @param mixed $value
 * @param InputStorageAye $dataLocator
 * @param ProcessRule ...$processRules
 * @return array{0:\Params\ValidationProblem[], 1:?mixed}
 * @throws Exception\ParamMissingException
 */
function processProcessingRules(
    $value,
    InputStorageAye $dataLocator,
    ProcessedValues $processedValues,
    ProcessRule ...$processRules
) {
    foreach ($processRules as $processRule) {
        $validationResult = $processRule->process($value, $processedValues, $dataLocator);
        if ($validationResult->anyErrorsFound()) {
            return [$validationResult->getValidationProblems(), null];
        }

        $value = $validationResult->getValue();
        // Set this here in case the rule happens to need to refer to the
        // current item by name
//            $this->paramValues[$path->getCurrentName()] = $value;
//            $dataLocator->storeCurrentResult($value);

        if ($validationResult->isFinalResult() === true) {
            break;
        }
    }

    // Set this here in case the subsequent rules are empty.
//        $this->paramValues[$path->getCurrentName()] = $value;
//        $dataLocator->storeCurrentResult($value);

    return [[], $value];
}


/**
 * @param \Params\InputParameter $param
 * @param ProcessedValuesImpl $paramValues
 * @param InputStorageAye $dataLocator
 * @return array{0:ValidationProblem[], 1:mixed}
 * @throws Exception\ParamMissingException
 */
function processInputParameter(
    InputParameter $param,
    ProcessedValuesImpl $paramValues,
    InputStorageAye $dataLocator
) {

    $dataLocatorForItem = $dataLocator->moveKey($param->getInputName());
    $extractRule = $param->getExtractRule();
    $validationResult = $extractRule->process(
        $paramValues, $dataLocatorForItem
    );

    if ($validationResult->anyErrorsFound()) {
        return [$validationResult->getValidationProblems(), null];
    }

    $value = $validationResult->getValue();

    // Process has already ended.
    if ($validationResult->isFinalResult() === true) {
        return [[], $value];
    }

    // Extract rule wasn't a final result, so process
    return processProcessingRules(
        $value,
        $dataLocatorForItem,
        $paramValues,
        ...$param->getProcessRules()
    );
}


/**
 * @param \Params\InputParameter[] $inputParameters
 * @param ProcessedValuesImpl $paramValues
 * @param InputStorageAye $dataLocator
 * @return \Params\ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputParameters(
    $inputParameters,
    ProcessedValuesImpl $paramValues,
    InputStorageAye $dataLocator
) {
    $validationProblems = [];

    foreach ($inputParameters as $inputParameter) {
        [$newValidationProblems, $value] = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataLocator
        );

        if (count($newValidationProblems) !== 0) {
            $validationProblems = [...$validationProblems, ...$newValidationProblems];
            continue;
        }

        $paramValues->setValue($inputParameter->getInputName(), $value);
    }

    // TODO - why does this return values as well?
    return $validationProblems;
}
