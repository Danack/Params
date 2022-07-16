<?php

namespace TypeSpec;

use TypeSpec\DataStorage\ArrayDataStorage;
use TypeSpec\DataStorage\ComplexDataStorage;
use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Exception\AnnotationClassDoesNotExistException;
use TypeSpec\Exception\IncorrectNumberOfParametersException;
use TypeSpec\Exception\InvalidDatetimeFormatException;
use TypeSpec\Exception\InvalidJsonPointerException;
use TypeSpec\Exception\LogicException;
use TypeSpec\Exception\MissingClassException;
use TypeSpec\Exception\MissingConstructorParameterNameException;
use TypeSpec\Exception\NoConstructorException;
use TypeSpec\Exception\PropertyHasMultipleInputTypeSpecAnnotationsException;
use TypeSpec\Exception\TypeDefinitionException;
use TypeSpec\Exception\TypeNotInputParameterListException;
use TypeSpec\Exception\ValidationException;
use TypeSpec\ExtractRule\GetType;
use TypeSpec\ProcessRule\ProcessPropertyRule;
use TypeSpec\Value\Ordering;

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
 * @return array{0:null, 1:\TypeSpec\ValidationProblem[]}|array{0:T[], 1:null}
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
    /** @var \TypeSpec\ValidationProblem[] $allValidationProblems */
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
 * @return InputTypeSpec[]
 * @throws TypeDefinitionException
 * @throws MissingClassException
 * @throws TypeNotInputParameterListException
 */
function getParamForClass(string $className): array
{
    return getInputTypeSpecListForClass($className);
}

/**
 * @param string $className
 * @return \TypeSpec\InputTypeSpec[]
 * @throws TypeDefinitionException
 * @throws MissingClassException
 * @throws TypeNotInputParameterListException
 */
function getInputTypeSpecListForClass(string $className): array
{
    if (class_exists($className) !== true) {
        throw MissingClassException::fromClassname($className);
    }

    // TODO - fold into single function
    $inputParameterList = getInputTypeSpecListFromAnnotations($className);

    if (count($inputParameterList) === 0) {
        $implementsInterface = is_subclass_of(
            $className,
            TypeSpec::class,
            $allow_string = true
        );

        if ($implementsInterface !== true) {
            throw TypeNotInputParameterListException::fromClassname($className);
        }

        // Type is okay, get data and validate
        $inputParameterList = call_user_func([$className, 'getInputTypeSpecList']);
    }
    // TODO - end fold into single function

    // Validate all entries are InputParameters
    $index = 0;
    foreach ($inputParameterList as $inputParameter) {
        if (!$inputParameter instanceof InputTypeSpec) {
            throw TypeDefinitionException::foundNonPropertyDefinition($index, $className);
        }

        $index += 1;
    }

    // All okay, array contains only Param items.
    /** @var \TypeSpec\InputTypeSpec[] $inputParameterList */
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
        throw IncorrectNumberOfParametersException::wrongNumber(
            $classname,
            count($constructor_params),
            $processedValues->getCount()
        );
    }

    $built_params = [];

    foreach ($constructor_params as $constructor_param) {
        $name = $constructor_param->getName();
        [$value, $available] = $processedValues->getValueForTargetProperty($name);
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
 * @param \TypeSpec\InputTypeSpec[] $params
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

    $validationProblems = processInputTypeSpecList(
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


/**
 * @template T
 * @param class-string<T> $classname
 * @param \TypeSpec\InputTypeSpec[] $params
 * @param DataStorage $dataStorage
 * @return array{0:?object, 1:\TypeSpec\ValidationProblem[]}
 * @throws Exception\TypeSpecException
 * @throws ValidationException
 *
 * The rules are passed separately to the classname so that we can
 * support rules coming both from static info and from factory objects.
 */
function createOrError($classname, $params, DataStorage $dataStorage)
{
    $paramsValuesImpl = new ProcessedValues();

    $validationProblems = processInputTypeSpecList(
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
 * @param ProcessPropertyRule ...$processRules
 * @return array{0:\TypeSpec\ValidationProblem[], 1:?mixed}
 * @throws Exception\ParamMissingException
 */
function processProcessingRules(
    $value,
    DataStorage $dataStorage,
    ProcessedValues $processedValues,
    ProcessPropertyRule ...$processRules
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
 * @param \TypeSpec\InputTypeSpec $param
 * @param ProcessedValues $paramValues
 * @param DataStorage $dataStorage
 * @return ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputTypeSpec(
    InputTypeSpec      $param,
    ProcessedValues    $paramValues,
    DataStorage        $dataStorage
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
 * @param PropertyInputTypeSpec $param
 * @param ProcessedValues $paramValues
 * @param DataStorage $dataStorage
 * @return ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processSingleInputParameter(
    PropertyInputTypeSpec    $param,
    ProcessedValues $paramValues,
    DataStorage     $dataStorage
): array {
    $knownInputNames = [];
    $inputParameter = $param->getInputTypeSpec();

    $knownInputNames[] = $inputParameter->getInputName();
    return processInputTypeSpec(
        $inputParameter,
        $paramValues,
        $dataStorage
    );
}


/**
 * @param \TypeSpec\InputTypeSpec[] $inputTypeSpecList
 * @param ProcessedValues $processedValues
 * @param DataStorage $dataStorage
 * @return \TypeSpec\ValidationProblem[]
 * @throws Exception\ParamMissingException
 */
function processInputTypeSpecList(
    array $inputTypeSpecList,
    ProcessedValues $processedValues,
    DataStorage $dataStorage
) {
    $validationProblems = [];

    $knownInputNames = [];

    foreach ($inputTypeSpecList as $inputParameter) {
        $knownInputNames[] = $inputParameter->getInputName();
        $newValidationProblems = processInputTypeSpec(
            $inputParameter,
            $processedValues,
            $dataStorage
        );

        if (count($newValidationProblems) !== 0) {
            $validationProblems = [...$validationProblems, ...$newValidationProblems];
            continue;
        }
    }

    // TODO - figure out what to do about unknown input parameters
    // See https://github.com/Danack/Params/issues/13
    /** @phpstan-ignore-next-line
     *  @psalm-suppress TypeDoesNotContainType
     */
    if (false) {
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
 * @return InputTypeSpec[]
 * @throws \ReflectionException
 */
function getInputTypeSpecListFromAnnotations(string $class): array
{
    $rc = new \ReflectionClass($class);
    $inputParameters = [];

    foreach ($rc->getProperties() as $property) {
        $attributes = $property->getAttributes();
        $current_property_has_typespec = false;
        foreach ($attributes as $attribute) {
            $rc_of_attribute = getReflectionClassOfAttribute(
                $class,
                $attribute->getName(),
                $property
            );
            $is_a_param = $rc_of_attribute->implementsInterface(PropertyInputTypeSpec::class);

            if ($is_a_param !== true) {
                continue;
            }

            if ($current_property_has_typespec == true) {
                throw PropertyHasMultipleInputTypeSpecAnnotationsException::create(
                    $class,
                    $property->getName()
                );
            }

            $current_property_has_typespec = true;
            $typeProperty = $attribute->newInstance();

            /** @var PropertyInputTypeSpec $typeProperty */
            $inputParameter = $typeProperty->getInputTypeSpec();
            $inputParameter->setTargetParameterName($property->getName());

            $inputParameters[] = $inputParameter;
        }
    }

    return $inputParameters;
}


/**
 * @param object $dto
 * @return array{0:?object, 1:\TypeSpec\ValidationProblem[]}
 * @throws Exception\TypeSpecException
 * @throws TypeDefinitionException
 * @throws MissingClassException
 * @throws TypeNotInputParameterListException
 * @throws ValidationException
 */
function validate(object $dto)
{
    $class = get_class($dto);

    /** @var class-string $class */
    $inputParameterList = getInputTypeSpecListForClass($class);

    $dataStorage = ComplexDataStorage::fromData($dto);

    [$object, $validationProblems] = createOrError(
        $class,
        $inputParameterList,
        $dataStorage
    );

    return [$object, $validationProblems];
}
