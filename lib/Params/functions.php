<?php

namespace Params;

use Params\Exception\InvalidDatetimeFormatException;
use Params\DataStorage\ArrayDataStorage;
use Params\DataStorage\DataStorage;
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
    $dataStorage = ArrayDataStorage::fromArray($data);
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
    $dataStorage = ArrayDataStorage::fromArray($data);
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
 * @param DataStorage $dataStorage
 * @param GetType $typeExtractor
 * @return ValidationResult
 */
function createArrayOfTypeFromInputStorage(
    DataStorage $dataStorage,
    GetType $typeExtractor
): ValidationResult {

    // Setup variables to hold data over loop.
    $items = [];
    /** @var \Params\ValidationProblem[] $allValidationProblems */
    $allValidationProblems = [];
    $paramsValuesImpl = new ProcessedValues();
    $index = 0;

    $itemData = $dataStorage->getCurrentValue();

    if (is_array($itemData) !== true) {
        return ValidationResult::errorResult($dataStorage, Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1);
    }

    foreach ($itemData as $key => $value) {
        $dataStorageForItem = $dataStorage->moveKey($key);

        $result = $typeExtractor->process(
            $paramsValuesImpl,
            $dataStorageForItem
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
 * @param ProcessedValues $processedValues
 * @return T of object
 * @throws \ReflectionException
 * @throws NoConstructorException
 */
function createObjectFromProcessedValues(string $classname, ProcessedValues $processedValues)
{
    $reflection_class = new \ReflectionClass($classname);

    $r_constructor = $reflection_class->getConstructor();

    // TODO - why do we forbid this? Although a class without constructor
    // is probably a mistake, it's not necessarily a mistake.
    if ($r_constructor === null) {
        throw NoConstructorException::noConstructor($classname);
    }

    if ($r_constructor->isPublic() !== true) {
        throw NoConstructorException::notPublicConstructor($classname);
    }

    $constructor_params = $r_constructor->getParameters();
    if (count($constructor_params) !== $processedValues->getCount()) {
        throw IncorrectNumberOfParamsException::wrongNumber(
            $classname,
            count($constructor_params),
            $processedValues->getCount()
        );
    }

    $built_params = [];

    foreach ($constructor_params as $constructor_param) {
        $name = $constructor_param->getName();
        [$value, $available] = $processedValues->getValueForTargetParam($name);
        if ($available !== true) {
            throw MissingConstructorParameterNameException::missingParam(
                $classname,
                $name
            );
        }
        $built_params[] = $value;
    }

    $object = $reflection_class->newInstanceArgs($built_params);

    /** @var T $object */
    return $object;
}


/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\InputParameter[] $params
 * @param DataStorage $dataStorage
 * @return T of object
 * @throws ValidationException
 * @throws \ReflectionException
 */
function create(
    $classname,
    $params,
    DataStorage $dataStorage
) {
    $processedValues = new ProcessedValues();

    $validationProblems = processInputParameters(
        $params,
        $processedValues,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        throw new ValidationException("Validation problems", $validationProblems);
    }
    $object = createObjectFromProcessedValues($classname, $processedValues);

    /** @var T $object */
    return $object;
}

///**
// * TODO - this isn't used?
// *
// *
// * @template T
// * @param string $class
// * @param \VarMap\VarMap $varMap
// * @psalm-param class-string<T> $class
// * @return T
// * @throws \ReflectionException
// * @throws ValidationException
// */
//function createTypeFromAnnotations(\VarMap\VarMap $varMap, string $class)
//{
//    $rules = getParamsFromAnnotations($class);
//
//    $dataStorage = ArrayDataStorage::fromArray($varMap->toArray());
//
//    $object = create(
//        $class,
//        $rules,
//        $dataStorage
//    );
//
//    return $object;
//}



/**
 * @template T
 * @param class-string<T> $classname
 * @param \Params\InputParameter[] $params
 * @param DataStorage $dataStorage
 * @return array{0:?object, 1:\Params\ValidationProblem[]}
 * @throws Exception\ParamsException
 * @throws ValidationException
 *
 * The rules are passed separately to the classname so that we can
 * support rules coming both from static info and from factory objects.
 */
function createOrError($classname, $params, DataStorage $dataStorage)
{
    $paramsValuesImpl = new ProcessedValues();

    $validationProblems = processInputParameters(
        $params,
        $paramsValuesImpl,
        $dataStorage
    );

    if (count($validationProblems) !== 0) {
        return [null, $validationProblems];
    }

    $object = createObjectFromProcessedValues($classname, $paramsValuesImpl);

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
 * @param DataStorage $dataStorage
 * @param ProcessRule ...$processRules
 * @return array{0:\Params\ValidationProblem[], 1:?mixed}
 * @throws Exception\ParamMissingException
 */
function processProcessingRules(
    $value,
    DataStorage $dataStorage,
    ProcessedValues $processedValues,
    ProcessRule ...$processRules
) {
    foreach ($processRules as $processRule) {
        $validationResult = $processRule->process($value, $processedValues, $dataStorage);
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
 * @param DataStorage $dataStorage
 * @return ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputParameter(
    InputParameter $param,
    ProcessedValues $paramValues,
    DataStorage $dataStorage
) {

    $dataStorageForItem = $dataStorage->moveKey($param->getInputName());
    $extractRule = $param->getExtractRule();
    $validationResult = $extractRule->process(
        $paramValues,
        $dataStorageForItem
    );

    if ($validationResult->anyErrorsFound()) {
        return $validationResult->getValidationProblems();
    }

    $value = $validationResult->getValue();

    // Process has already ended.
    if ($validationResult->isFinalResult() === true) {
        // TODO - modify here
        $paramValues->setValue($param, $value);
        return [];
    }

    // Extract rule wasn't a final result, so process
    [$validationProblems, $value] = processProcessingRules(
        $value,
        $dataStorageForItem,
        $paramValues,
        ...$param->getProcessRules()
    );

    if (count($validationProblems) === 0) {
        // TODO - modify here
        $paramValues->setValue($param, $value);
    }

    return $validationProblems;
}


/**
 * @param \Params\InputParameter[] $inputParameters
 * @param ProcessedValues $paramValues
 * @param DataStorage $dataStorage
 * @return \Params\ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputParameters(
    array $inputParameters,
    ProcessedValues $paramValues,
    DataStorage $dataStorage
) {
    $validationProblems = [];

    $knownInputNames = [];

    foreach ($inputParameters as $inputParameter) {
        $knownInputNames[] = $inputParameter->getInputName();
        $newValidationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataStorage
        );

        if (count($newValidationProblems) !== 0) {
            $validationProblems = [...$validationProblems, ...$newValidationProblems];
            continue;
        }
    }

    $current_values = $dataStorage->getCurrentValue();

    foreach ($current_values as $key => $value) {
        if (in_array($key, $knownInputNames, true) === false) {
            $message = sprintf(
                Messages::UNKNOWN_INPUT_PARAMETER,
                $key
            );

            $validationProblems[] = new ValidationProblem(
                $dataStorage,
                $message
            );
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

///**
// * @template T of object
// * @param \ReflectionClass<T> $rc_param - the reflection class of the attribute
// * @param \ReflectionAttribute $attribute - the attribute itself
// * @param string $defaultName - a default name to use.
// * @return T
// * @throws \ReflectionException
// */
//function instantiateParam(
//    \ReflectionClass $rc_param,
//    \ReflectionAttribute $attribute,
//    string $defaultName
//): object {
//
//    // TODO - maybe replace this code with $attribute->newInstance();
//    // But that means every param needs to have it's name listed...
//    // which is probably not the worst thing ever.
//    $param_constructor = $rc_param->getConstructor();
//
//    if ($param_constructor === null) {
//        // Need an example usage of this.
//        return $rc_param->newInstance();
//    }
//
//    $param_constructor_parameters = $param_constructor->getParameters();
//    $args = $attribute->getArguments();
//    $argsByName = [];
//
//    $count = 0;
//    foreach ($param_constructor_parameters as $param_constructor_parameter) {
//        if ($count >= count($args)) {
//            break;
//        }
//        $name = $param_constructor_parameter->getName();
//        $argsByName[$name] = $args[$count];
//        $count += 1;
//    }
//    $has_name = false;
//    foreach ($param_constructor_parameters as $param_constructor_parameter) {
//        if ($param_constructor_parameter->getName() === 'name') {
//            $has_name = true;
//        }
//    }
//
//    // if the constructor expects a name, set the default one
//    // if none was set.
//    if ($has_name) {
//        if (array_key_exists('name', $argsByName) !== true) {
//            $argsByName['name'] = $defaultName;
//        }
//    }
//
//    return $rc_param->newInstance(...$argsByName);
//}


function getReflectionClassOfAttribute(
    string $class,
    string $attributeClassname,
    \ReflectionProperty $property
): \ReflectionClass {
    if (class_exists($attributeClassname, true) !== true) {
        throw AnnotationClassDoesNotExistException::create(
            $class,
            $property->getName(),
            $attributeClassname
        );
    }

    return new \ReflectionClass($attributeClassname);
}

/**
 * @template T
 * @param string|object $class
 * @psalm-param class-string<T> $class
 * @return InputParameter[]
 * @throws \ReflectionException
 */
function getParamsFromAnnotations(string $class): array
{
    $rc = new \ReflectionClass($class);
    $inputParameters = [];

    foreach ($rc->getProperties() as $property) {
        $attributes = $property->getAttributes();
        $current_property_has_param = false;
        foreach ($attributes as $attribute) {
            $rc_of_attribute = getReflectionClassOfAttribute(
                $class,
                $attribute->getName(),
                $property
            );
            $is_a_param = $rc_of_attribute->implementsInterface(Param::class);

            if ($is_a_param !== true) {
                continue;
            }

            if ($current_property_has_param == true) {
                throw PropertyHasMultipleParamAnnotationsException::create(
                    $class,
                    $property->getName()
                );
            }

            $current_property_has_param = true;
            $param = $attribute->newInstance();

            /** @var Param $param */
            $inputParameter = $param->getInputParameter();
            $inputParameter->setTargetParameterName($property->getName());

            $inputParameters[] = $inputParameter;
        }
    }

    return $inputParameters;
}
