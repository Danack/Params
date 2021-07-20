<?php

namespace Params;

use Params\Exception\InvalidDatetimeFormatException;
use Params\InputStorage\ArrayInputStorage;
use Params\InputStorage\InputStorage;
use Params\Exception\InputParameterListException;
use Params\Exception\InvalidJsonPointerException;
use Params\Exception\LogicException;
use Params\Exception\MissingClassException;
use Params\Exception\TypeNotInputParameterListException;
use Params\Exception\ValidationException;
use Params\ExtractRule\GetType;
use Params\ProcessRule\ProcessRule;
use Params\Value\Ordering;
use Params\Exception\NoConstructorException;
use Params\Exception\IncorrectNumberOfParamsException;
use Params\Exception\MissingConstructorParameterNameException;
use Params\Exception\PropertyHasMultipleParamAnnotationsException;
use Params\Exception\AnnotationClassDoesNotExistException;

/**
 * @template T
 * @param string $type
 * @psalm-param class-string<T> $type
 * @param array $data
 * @return T[]
 * @throws ValidationException
 */
function createArrayOfType(string $type, array $data): array
{
    $dataStorage = ArrayInputStorage::fromArray($data);
    $getType = GetType::fromClass($type);
    $validationResult = createArrayOfTypeFromInputStorage($dataStorage, $getType);

    if ($validationResult->anyErrorsFound()) {
        throw new ValidationException(
            "Validation problems",
            $validationResult->getValidationProblems()
        );
    }

    return $validationResult->getValue();
}


/**
 * @template T
 * @param string $type
 * @psalm-param class-string<T> $type
 * @param array $data
 * @return array{null, \Params\ValidationProblem[]}|array{T[], null}
 */
function createArrayOfTypeOrError(string $type, array $data): array
{
    $dataStorage = ArrayInputStorage::fromArray($data);
    $getType = GetType::fromClass($type);
    $validationResult = createArrayOfTypeFromInputStorage($dataStorage, $getType);

    if ($validationResult->anyErrorsFound()) {
        return [null, $validationResult->getValidationProblems()];
    }

    $finalValue = $validationResult->getValue();
    /** @var T[] $finalValue */

    return [$finalValue, null];
}

/**
 *
 *
 * @param InputStorage $dataLocator
 * @param GetType $typeExtractor
 * @return ValidationResult
 */
function createArrayOfTypeFromInputStorage(
    InputStorage $dataLocator,
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

    // TODO - fold into single function
    $inputParameterList = getParamsFromAnnotations($className);

    if (count($inputParameterList) === 0) {
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
    }
    // TODO - end fold into single function

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
 * @throws NoConstructorException
 */
function createObjectFromParams(string $classname, array $values)
{
    $reflection_class = new \ReflectionClass($classname);
//    if ($reflection_class->hasMethod('__construct') !== true) {
//        throw NoConstructorException::noConstructor($classname);
//    }
    $r_constructor = $reflection_class->getConstructor();

    if ($r_constructor === null) {
        throw NoConstructorException::noConstructor($classname);
    }

    if ($r_constructor->isPublic() !== true) {
        throw NoConstructorException::notPublicConstructor($classname);
    }

    $params = $r_constructor->getParameters();
    if (count($params) !== count($values)) {
        throw IncorrectNumberOfParamsException::wrongNumber(
            $classname,
            count($params),
            count($values)
        );
    }

    foreach ($params as $param) {
        $name = $param->getName();
        if (array_key_exists($name, $values) !== true) {
            throw MissingConstructorParameterNameException::missingParam(
                $classname,
                $name
            );
        }
    }

    $object = $reflection_class->newInstanceArgs($values);

    /** @var T $object */
    return $object;
}


/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\InputParameter[] $params
 * @param ArrayInputStorage $dataLocator
 * @return T of object
 * @throws ValidationException
 * @throws \ReflectionException
 */
function create(
    $classname,
    $params,
    ArrayInputStorage $dataLocator
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
 * @param string $class
 * @param \VarMap\VarMap $varMap
 * @psalm-param class-string<T> $class
 * @return T
 * @throws \ReflectionException
 * @throws ValidationException
 */
function createTypeFromAnnotations(\VarMap\VarMap $varMap, string $class)
{
    $rules = getParamsFromAnnotations($class);

    $dataLocator = ArrayInputStorage::fromVarMap($varMap);

    $object = create(
        $class,
        $rules,
        $dataLocator
    );

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
function createOrError($classname, $params, ArrayInputStorage $dataLocator)
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
 * @param InputStorage $dataLocator
 * @param ProcessRule ...$processRules
 * @return array{0:\Params\ValidationProblem[], 1:?mixed}
 * @throws Exception\ParamMissingException
 */
function processProcessingRules(
    $value,
    InputStorage $dataLocator,
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
 * @param InputStorage $dataLocator
 * @return ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputParameter(
    InputParameter $param,
    ProcessedValues $paramValues,
    InputStorage $dataLocator
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
 * @param InputStorage $dataLocator
 * @return \Params\ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputParameters(
    array $inputParameters,
    ProcessedValues $paramValues,
    InputStorage $dataLocator
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

/**
 * Get the list of default supported DateTime formats
 * @return string[]
 */
function getDefaultSupportedTimeFormats(): array
{
    return [
        \DateTime::ATOM,
        \DateTime::COOKIE,
        \DateTime::ISO8601,
        \DateTime::RFC822,
        \DateTime::RFC850,
        \DateTime::RFC1036,
        \DateTime::RFC1123,
        \DateTime::RFC2822,
        \DateTime::RFC3339,
        \DateTime::RFC3339_EXTENDED,
        \DateTime::RFC7231,
        \DateTime::RSS,
        \DateTime::W3C,
    ];
}

/**
 * @param string[] $allowedFormats
 * @return string[]
 * @throws InvalidDatetimeFormatException
 * @psalm-suppress DocblockTypeContradiction
 * @psalm-suppress RedundantConditionGivenDocblockType
 */
function checkAllowedFormatsAreStrings(array $allowedFormats): array
{
    $position = 0;
    foreach ($allowedFormats as $allowedFormat) {
        if (is_string($allowedFormat) !== true) {
            throw InvalidDatetimeFormatException::stringRequired($position, $allowedFormat);
        }
        $position += 1;
    }

    return $allowedFormats;
}

/**
 * @template T
 * @param string|object $class
 * @psalm-param class-string<T>|object $class
 * @return InputParameter[]
 * @throws \ReflectionException
 */
function getParamsFromAnnotations(string|object $class): array
{
    $rc = new \ReflectionClass($class);
    $inputParameters = [];

    foreach ($rc->getProperties() as $property) {
        $attributes = $property->getAttributes();
        $current_property_has_param = false;
        foreach ($attributes as $attribute) {
            $classname = $attribute->getName();
            if (class_exists($classname, true) !== true) {
                if (is_object($class) === true) {
                    $classAsString = get_class($class);
                }
                else {
                    $classAsString = $class;
                }

                throw AnnotationClassDoesNotExistException::create(
                    $classAsString,
                    $property->getName(),
                    $classname
                );
            }

            $rc_param = new \ReflectionClass($classname);
            $is_a_param = $rc_param->implementsInterface(Param::class);

            if ($is_a_param !== true) {
                continue;
            }

            if ($current_property_has_param == true) {
                if (is_object($class) === true) {
                    $classAsString = get_class($class);
                }
                else {
                    $classAsString = $class;
                }
                throw PropertyHasMultipleParamAnnotationsException::create(
                    $classAsString,
                    $property->getName()
                );
            }

            $current_property_has_param = true;
            $param_constructor = $rc_param->getConstructor();
            if ($param_constructor === null) {
                $param = $rc_param->newInstance();
            }
            else {
                $param_constructor_parameters = $param_constructor->getParameters();
                $args = $attribute->getArguments();
                $argsByName = [];

                $count = 0;
                foreach ($param_constructor_parameters as $param_constructor_parameter) {
                    if ($count >= count($args)) {
                        break;
                    }
                    $name = $param_constructor_parameter->getName();
                    $argsByName[$name] = $args[$count];
                    $count += 1;
                }

                if (array_key_exists('name', $argsByName) !== true) {
                    $argsByName['name'] = $property->getName();
                }

                $param = $rc_param->newInstance(...$argsByName);
            }
            /** @var Param $param */
            $inputParameters[] = $param->getInputParameter();
        }
    }

    return $inputParameters;
}
