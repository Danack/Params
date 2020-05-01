<?php

namespace Params;

use Params\DataLocator\DataStorage;
use Params\DataLocator\InputStorageAye;
use Params\Exception\InputParameterListException;
use Params\Exception\InvalidJsonPointerException;
use Params\Exception\LogicException;
use Params\Exception\MissingClassException;
use Params\Exception\TypeNotInputParameterListException;
use Params\Exception\ValidationException;
use Params\ExtractRule\GetType;
use Params\ProcessRule\ProcessRule;
use Params\Value\Ordering;

/**
 * TODO - make itemData not mixed.
 *
 * @param InputStorageAye $dataLocator
 * @param GetType $typeExtractor
 * @return ValidationResult
 */
function createArrayOfType(
    InputStorageAye $dataLocator,
    GetType $typeExtractor
): ValidationResult {

    // Setup variables to hold data over loop.
    $items = [];
    /** @var \Params\ValidationProblem[] $allValidationProblems */
    $allValidationProblems = [];
    $paramsValuesImpl = new ProcessedValues();
    $index = 0;

    $itemData = $dataLocator->getCurrentValue();

    if (is_array($itemData) !== true) {
        return ValidationResult::errorResult($dataLocator, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
    }

    foreach ($itemData as $key => $value) {
        $dataLocatorForItem = $dataLocator->moveKey($key);

        $result = $typeExtractor->process(
            $paramsValuesImpl,
            $dataLocatorForItem
        );

        if ($result->anyErrorsFound() === true) {
            $allValidationProblems = [...$allValidationProblems, ...$result->getValidationProblems()];
        }
        else {
            $items[$index] = $result->getValue();
        }

        $index += 1;
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
    // @phpstan-ignore-next-line
    $inputParameterList = call_user_func([$className, 'getInputParameterList']);

    // Validate all entries are InputParameters
    $index = 0;
    foreach ($inputParameterList as $inputParameter) {
        if (!$inputParameter instanceof InputParameter) {
            throw InputParameterListException::foundNonInputParameter($index, $className);
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
 * @template T
 * @param class-string<T> $classname
 * @param \Params\InputParameter[] $params
 * @param DataStorage $dataLocator
 * @return T of object
 * @throws ValidationException
 * @throws \ReflectionException
 */
function create(
    $classname,
    $params,
    DataStorage $dataLocator
) {
    $paramsValuesImpl = new ProcessedValues();

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
function createOrError($classname, $params, DataStorage $dataLocator)
{
    $paramsValuesImpl = new ProcessedValues();


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
 * @param string $pointer
 * @return array<string|int>
 */
function getJsonPointerParts(string $pointer): array
{
    if ($pointer === '') {
        return [];
    }

    if ($pointer[0] !== '/') {
        throw InvalidJsonPointerException::invalidFirstCharacter();
    }

    $remainingString = substr($pointer, 1);

    $parts = explode('/', $remainingString);

    $partsDecoded = [];

    foreach ($parts as $part) {
        $int = intval($part);

        // It was an int, use as int
        if ((string)$int === $part) {
            $partsDecoded[] = $int;
        }
        else {
            $partsDecoded[] = unescapeJsonPointer($part);
        }
    }

    // TODO - normalise digits to integers?
    return $partsDecoded;
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
        // @codeCoverageIgnoreStart
        // This seems impossible to test.
        throw new LogicException("preg_match failed");
        // @codeCoverageIgnoreEnd
    }

    if ($count !== 0) {
        $badCharPosition = $matches[0][1];
        $message = sprintf(
            Messages::INT_REQUIRED_FOUND_NON_DIGITS,
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
        if ($validationResult->isFinalResult() === true) {
            break;
        }
    }

    return [[], $value];
}


/**
 * @param \Params\InputParameter $param
 * @param ProcessedValues $paramValues
 * @param InputStorageAye $dataLocator
 * @return ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputParameter(
    InputParameter $param,
    ProcessedValues $paramValues,
    InputStorageAye $dataLocator
) {

    $dataLocatorForItem = $dataLocator->moveKey($param->getInputName());
    $extractRule = $param->getExtractRule();
    $validationResult = $extractRule->process(
        $paramValues,
        $dataLocatorForItem
    );

    if ($validationResult->anyErrorsFound()) {
        return $validationResult->getValidationProblems();
    }

    $value = $validationResult->getValue();

    // Process has already ended.
    if ($validationResult->isFinalResult() === true) {
        $paramValues->setValue($param->getInputName(), $value);
        return [];
    }

    // Extract rule wasn't a final result, so process
    [$validationProblems, $value] = processProcessingRules(
        $value,
        $dataLocatorForItem,
        $paramValues,
        ...$param->getProcessRules()
    );

    if (count($validationProblems) === 0) {
        $paramValues->setValue($param->getInputName(), $value);
    }

    return $validationProblems;
}


/**
 * @param \Params\InputParameter[] $inputParameters
 * @param ProcessedValues $paramValues
 * @param InputStorageAye $dataLocator
 * @return \Params\ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputParameters(
    array $inputParameters,
    ProcessedValues $paramValues,
    InputStorageAye $dataLocator
) {
    $validationProblems = [];

    foreach ($inputParameters as $inputParameter) {
        $newValidationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataLocator
        );

        if (count($newValidationProblems) !== 0) {
            $validationProblems = [...$validationProblems, ...$newValidationProblems];
            continue;
        }
    }

    return $validationProblems;
}

/**
 * Converts a string into the raw bytes, and displays
 * @param string $string
 * @return string
 */
function getRawCharacters(string $string): string
{
    $resultInHex = bin2hex($string);
    $resultSeparated = implode(', ', str_split($resultInHex, 2)); //byte safe

    return $resultSeparated;
}
