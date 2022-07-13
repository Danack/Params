<?php

declare(strict_types=1);

namespace TypeSpecTest;

use TypeSpec\Exception\AnnotationClassDoesNotExistException;
use TypeSpec\Exception\IncorrectNumberOfParamsException;
use TypeSpec\Exception\MissingConstructorParameterNameException;
use TypeSpec\Exception\PropertyHasMultipleParamAnnotationsException;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\DataStorage\DataStorage;
use TypeSpec\Exception\TypeDefinitionException;
use TypeSpec\Exception\TypeNotInputParameterListException;
use TypeSpec\ExtractRule\ExtractPropertyRule;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\ExtractRule\GetString;
use TypeSpec\InputTypeSpec;
use TypeSpec\Messages;
use TypeSpec\TypeProperty;
use TypeSpec\ProcessedValue;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\AlwaysEndsRule;
use TypeSpec\ProcessRule\ImagickIsRgbColor;
use TypeSpec\ProcessRule\MinLength;
use TypeSpec\OpenApi\ParamDescription;
use TypeSpec\Value\Ordering;
use TypeSpec\Exception\MissingClassException;
use TypeSpec\ProcessRule\AlwaysErrorsRule;
use TypeSpec\ExtractRule\GetType;
use TypeSpec\ValidationResult;
use TypeSpec\Exception\ValidationException;
use VarMap\ArrayVarMap;
use TypeSpecTest\Integration\FooParams;
use TypeSpec\Exception\NoConstructorException;
use TypeSpecTest\PropertyTypes\Quantity;
use function TypeSpec\unescapeJsonPointer;
use function TypeSpec\array_value_exists;
use function TypeSpec\check_only_digits;
use function TypeSpec\normalise_order_parameter;
use function TypeSpec\escapeJsonPointer;
use function TypeSpec\getRawCharacters;
use function TypeSpec\getInputTypeSpecListForClass;
use function TypeSpec\processInputParameters;
use function TypeSpec\processInputParameter;
use function TypeSpec\processProcessingRules;
use function TypeSpec\createArrayOfTypeFromInputStorage;
use function TypeSpec\createArrayOfType;
use function TypeSpec\createArrayOfTypeOrError;
use function TypeSpec\checkAllowedFormatsAreStrings;
use function TypeSpec\getInputTypeSpecListFromAnnotations;
use function TypeSpec\getDefaultSupportedTimeFormats;
use function TypeSpec\getReflectionClassOfAttribute;
use function TypeSpec\createObjectFromProcessedValues;
use function TypeSpec\processSingleInputParameter;
use function TypeSpec\getParamForClass;

/**
 * @coversNothing
 */
class FunctionsTest extends BaseTestCase
{
    public function providesNormaliseOrderParameter()
    {
        return [
            ['foo', 'foo', Ordering::ASC],
            ['+foo', 'foo', Ordering::ASC],
            ['-foo', 'foo', Ordering::DESC],
        ];
    }

    /**
     * @dataProvider providesNormaliseOrderParameter
     * @covers ::TypeSpec\normalise_order_parameter
     */
    public function testNormaliseOrderParameter($input, $expectedName, $expectedOrder)
    {
        list($name, $order) = normalise_order_parameter($input);

        $this->assertEquals($expectedName, $name);
        $this->assertEquals($expectedOrder, $order);
    }

    /**
     * @covers ::TypeSpec\check_only_digits
     */
    public function testCheckOnlyDigits()
    {
        // An integer gets short circuited
        $errorMsg = check_only_digits(12345);
        $this->assertNull($errorMsg);

        // Correct string passes through
        $errorMsg = check_only_digits('12345');
        $this->assertNull($errorMsg);

        // Incorrect string passes through
        $errorMsg = check_only_digits('123X45');

        // TODO - update string matching.
        $this->assertStringMatchesFormat("%sposition 3%s", $errorMsg);
    }

    /**
     * @covers ::TypeSpec\array_value_exists
     */
    public function testArrayValueExists()
    {
        $values = [
            '1',
            '2',
            '3'
        ];

        $foundExactType = array_value_exists($values, '2');
        $this->assertTrue($foundExactType);

        $foundJuggledType = array_value_exists($values, 2);
        $this->assertFalse($foundJuggledType);
    }


    public function providesEscapeJsonPointer()
    {
        return [

            ["a/b", "a~1b"],
            ["m~n", "m~0n"],

            ["~/0", "~0~10"],
            ["~/2", "~0~12"],
        ];
    }


    /**
     * @dataProvider providesEscapeJsonPointer
     * @covers ::\TypeSpec\escapeJsonPointer
     */
    public function testEscapeJsonPointer($unescaped, $expectedEscaped)
    {
        $actualEscaped = escapeJsonPointer($unescaped);
        $this->assertSame($expectedEscaped, $actualEscaped);
    }

    /**
     * @dataProvider providesEscapeJsonPointer
     * @covers ::TypeSpec\unescapeJsonPointer
     */
    public function testUnescapeJsonPointer($expectedUnescaped, $escaped)
    {
        $actualUnescaped = unescapeJsonPointer($escaped);
        $this->assertSame($expectedUnescaped, $actualUnescaped);
    }

//    /**
//     * @covers \Params\Functions::addChildErrorMessagesForArray
//     */
//    public function testaddChildErrorMessagesForArray()
//    {
//        $name = 'foo';
//        $message = 'Something went wrong.';
//        $problems = [
//            '/bar' => $message
//        ];
//
//        $problems = Functions::addChildErrorMessagesForArray(
//            $name,
//            $problems,
//            []
//        );
//
//        $expectedResult = [
//            '/foo/bar' => $message
//        ];
//
//        $this->assertSame($expectedResult, $problems);
//    }

    public function provides_getRawCharacters()
    {
        yield ['Hello', '48, 65, 6c, 6c, 6f'];
        yield ["ÁGUEDA", 'c3, 81, 47, 55, 45, 44, 41'];
        yield ["☺😎😋😂", 'e2, 98, ba, f0, 9f, 98, 8e, f0, 9f, 98, 8b, f0, 9f, 98, 82'];
    }

    /**
     * @dataProvider provides_getRawCharacters
     * @covers ::\TypeSpec\getRawCharacters
     * @param string $inputString
     * @param $expectedOutput
     */
    public function test_getRawCharacters(string $inputString, $expectedOutput)
    {
        $actualOutput = getRawCharacters($inputString);
        $this->assertSame($expectedOutput, $actualOutput);
    }

    /**
     * @covers ::\TypeSpec\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams()
    {
        $name = 'John';
        $age = 34;

        $object = \TypeSpec\createObjectFromProcessedValues(
            \TestObject::class,
            createProcessedValuesFromArray([
                'name' => $name,
                'age' => $age
            ])
        );

        $this->assertInstanceOf(\TestObject::class, $object);
        $this->assertSame($name, $object->getName());
        $this->assertSame($age, $object->getAge());
    }

    /**
     * @covers ::\TypeSpec\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_out_of_order()
    {
        $nameValue = 'John';
        $ageValue = 36;

        $object = \TypeSpec\createObjectFromProcessedValues(
            \TestObject::class,
            createProcessedValuesFromArray([
                'age' => $ageValue,
                'name' => $nameValue
            ])
        );

        $this->assertInstanceOf(\TestObject::class, $object);
        $this->assertSame($ageValue, $object->getAge());
        $this->assertSame($nameValue, $object->getName());
    }

    /**
     * @covers ::\TypeSpec\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_no_constructor()
    {
        $this->expectExceptionMessageMatchesTemplateString(Messages::CLASS_LACKS_CONSTRUCTOR);
        $this->expectException(\TypeSpec\Exception\NoConstructorException::class);
        createObjectFromProcessedValues(
            \OneColorNoConstructor::class,
            createProcessedValuesFromArray([])
        );
    }

    /**
     * @covers ::\TypeSpec\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_private_constructor()
    {
        $this->expectExceptionMessageMatchesTemplateString(Messages::CLASS_LACKS_PUBLIC_CONSTRUCTOR);
        $this->expectException(\TypeSpec\Exception\NoConstructorException::class);
        createObjectFromProcessedValues(
            \ThreeColorsPrivateConstructor::class,
            createProcessedValuesFromArray([])
        );
    }

    /**
     * @covers ::\TypeSpec\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_wrong_number_params()
    {
        $this->expectException(IncorrectNumberOfParamsException::class);
        createObjectFromProcessedValues(
            \NotActuallyAParam::class,
            createProcessedValuesFromArray([])
        );
    }

    /**
     * @covers ::\TypeSpec\createObjectFromProcessedValues
     */
    public function test_CreateObjectFromParams_missing_param()
    {
        $this->expectException(MissingConstructorParameterNameException::class);
        createObjectFromProcessedValues(
            \NotActuallyAParam::class,
            createProcessedValuesFromArray([
                'name' => 'John',
                'this_is_invalid' => 'Foo'
            ])
        );
    }

    /**
     * @covers ::\TypeSpec\createTypeFromAnnotations
     * @group deadish
     */
    public function test_createTypeFromAnnotations()
    {
        $varMap = new ArrayVarMap([
            'background_color' => 'red',
            'stroke_color' => 'rgb(255, 0, 255)',
            'fill_color' => 'white',
        ]);

        $result = createTypeFromAnnotations($varMap, \ThreeColors::class);

        $this->assertInstanceOf(\ThreeColors::class, $result);
    }

    public function provides_getJsonPointerParts()
    {
        yield ['/[3]', [3]];
        yield ['/', []];
        yield ['/[0]', [0]];

        yield ['/[0]/foo', [0, 'foo']];
        yield ['/[0]/foo[2]', [0, 'foo', 2]];
        yield ['/foo', ['foo']];
        yield ['/foo[2]', ['foo', 2]];

        yield ['/foo/bar', ['foo', 'bar']];
        yield ['/foo/bar[3]', ['foo', 'bar', 3]];
    }

    /**
     * @dataProvider provides_getJsonPointerParts
     * @covers ::\TypeSpec\getJsonPointerParts
     * @param $input
     * @param $expected
     */
    public function test_getJsonPointerParts($input, $expected)
    {
        $message = "We should move to actually support json pointer correctly to make it easier to implement";
        $message .= "Also, I can't remember what the correct behaviour is meant to be here.";
        // The current behaviour is probably wrong for json pointer...there is a conflict
        // between following that and useful (to PHP developers) error messages.

        $this->markTestSkipped($message);
        $actual = \TypeSpec\getJsonPointerParts($input);
        $this->assertSame($expected, $actual);
    }

    /**
     * @covers ::\TypeSpec\getInputTypeSpecListForClass
     */
    public function test_getInputParameterListForClass()
    {
        $inputParameters = getInputTypeSpecListForClass(\TestParams::class);
        $this->assertCount(1, $inputParameters);
    }

    /**
     * @covers ::\TypeSpec\getInputTypeSpecListForClass
     */
    public function test_getInputParameterListForClass_missing_class()
    {
        $this->expectException(MissingClassException::class);
        $inputParameters = getInputTypeSpecListForClass("does_not_exist");
    }

    /**
     * @covers ::\TypeSpec\getInputTypeSpecListForClass
     */
    public function test_getInputParameterListForClass_missing_implements()
    {
        $this->expectException(TypeNotInputParameterListException::class);
        $inputParameters = getInputTypeSpecListForClass(
            \DoesNotImplementInputParameterList::class
        );
    }

    /**
     * @covers ::\TypeSpec\getInputTypeSpecListForClass
     */
    public function test_getInputParameterListForClass_non_inputparameter()
    {
        $this->expectException(TypeDefinitionException::class);
        $inputParameters = getInputTypeSpecListForClass(
            \ReturnsBadTypeSpec::class
        );
    }





    /**
     * @covers ::\TypeSpec\getParamForClass
     */
    public function test_getParamForClass()
    {
        $param = getParamForClass(\TestParams::class);
    }



    /**
     * @group wip
     */
    public function test_processSingleInputParameter()
    {
        $inputValue = 5;

        $paramValues  = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromArray([
            'foo' => $inputValue,
        ]);

        $param = new Quantity('foo');

        $result = processSingleInputParameter(
            $param,
            $paramValues,
            $dataStorage
        );

        $this->assertEmpty($result);

        $values = $paramValues->getAllValues();
        $this->assertCount(1, $values);
        $this->assertSame($inputValue, $values['foo']);
    }

    /**
     * @covers ::\TypeSpec\processInputParameters
     */
    public function test_processInputParameters()
    {
        $inputParameters = \AlwaysErrorsParams::getInputTypeSpecList();
        $dataStorage = TestArrayDataStorage::fromArray([
            'foo' => 'foo string',
            'bar' => 'bar string'
        ]);

        $paramValues  = new ProcessedValues();
        $validationProblems = processInputParameters(
            $inputParameters,
            $paramValues,
            $dataStorage
        );

        $this->assertValidationProblem(
            '/bar',
            \AlwaysErrorsParams::ERROR_MESSAGE,
            $validationProblems
        );
        $this->assertCount(1, $validationProblems);
    }


    /**
     * @covers ::\TypeSpec\processProcessingRules
     */
    public function test_processProcessingRules_works()
    {
        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'bar string'
        ]);
        $dataStorage = $dataStorage->moveKey('bar');

        $paramValues  = new ProcessedValues();
        $minLength = new MinLength(5);
        $message = "forced ending";
        $alwaysEnds = new AlwaysEndsRule($message);
        $alwaysError = new AlwaysErrorsRule('There was error');

        $inputValue = 'Hello world';

        [$validationProblems, $resultValue] = processProcessingRules(
            $inputValue,
            $dataStorage,
            $paramValues,
            $minLength,
            $alwaysEnds,
            $alwaysError
        );

        $this->assertSame($message, $resultValue);
        $this->assertCount(0, $validationProblems);
    }

    /**
     * @covers ::\TypeSpec\processProcessingRules
     */
    public function test_processProcessingRules_errors()
    {
        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'bar string'
        ]);
        $dataStorage = $dataStorage->moveKey('bar');

        $errorMessage = 'There was error';
        $paramValues  = new ProcessedValues();
        $alwaysError = new AlwaysErrorsRule($errorMessage);

        $value = 'Hello world';

        [$validationProblems, $value] = processProcessingRules(
            $value,
            $dataStorage,
            $paramValues,
            $alwaysError
        );

        $this->assertNull($value);

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblem(
            '/bar',
            $errorMessage,
            $validationProblems
        );
    }


    /**
     * @covers ::\TypeSpec\createArrayOfTypeFromInputStorage
     */
    public function test_createArrayOfType_works()
    {
        $data = [
            ['name' => 'John 1'],
            ['name' => 'John 2'],
            ['name' => 'John 3'],
        ];

        $dataStorage = TestArrayDataStorage::fromArray($data);
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertFalse($result->anyErrorsFound());

        $items = $result->getValue();
        $this->assertCount(3, $items);

        $count = 1;

        foreach ($items as $item) {
            $this->assertInstanceOf(\TestParams::class, $item);
            $this->assertSame('John ' . $count, $item->getName());
            $count += 1;
        }
    }


    /**
     * @covers ::\TypeSpec\createArrayOfTypeFromInputStorage
     */
    public function test_createArrayOfType_bad_data()
    {
        $data = [
            ['name' => 'John 1'],
            ['name' => 'John 2'],
            ['name_this_is_typo' => 'John 3'],
        ];

        $dataStorage = TestArrayDataStorage::fromArray($data);
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertInstanceOf(ValidationResult::class, $result);
        $this->assertTrue($result->anyErrorsFound());

        $this->assertValidationProblem(
            '/[2]/name',
            Messages::VALUE_NOT_SET,
            $result->getValidationProblems()
        );
    }


    /**
     * @covers ::\TypeSpec\createArrayOfTypeFromInputStorage
     */
    public function test_createArrayOfType_not_array_data()
    {
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', 'bar');
        $getType = GetType::fromClass(\TestParams::class);

        $result = createArrayOfTypeFromInputStorage(
            $dataStorage,
            $getType
        );

        $this->assertTrue($result->anyErrorsFound());

        $this->assertValidationProblem(
            '/foo',
            Messages::ERROR_MESSAGE_NOT_ARRAY_VARIANT_1,
            $result->getValidationProblems()
        );
        $this->assertCount(1, $result->getValidationProblems());
    }


    /**
     * @covers ::\TypeSpec\processInputParameter
     */
    public function test_processInputParameter_works()
    {
        $inputParameter = new InputTypeSpec(
            'bar',
            new GetString()
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'bar string'
        ]);

        $paramValues  = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataStorage
        );

        $this->assertCount(0, $validationProblems);

        $this->assertTrue($paramValues->hasValue('bar'));
        $this->assertSame('bar string', $paramValues->getValue('bar'));
    }


    /**
     * @covers ::\TypeSpec\processInputParameter
     */
    public function test_processInputParameter_errors_on_extract()
    {

        $inputParameter = new InputTypeSpec(
            'bar',
            new GetInt()
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'This is not an integer'
        ]);

        $paramValues = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataStorage
        );

        $this->assertValidationProblem(
            '/bar',
            Messages::INT_REQUIRED_FOUND_NON_DIGITS2,
            $validationProblems
        );
        $this->assertCount(1, $validationProblems);
    }

    /**
     * @covers ::\TypeSpec\processInputParameter
     */
    public function test_processInputParameter_extract_ends_processing()
    {
        $value = 12345;

        $extractIsFinal = new class($value) implements ExtractPropertyRule  {

            private $value;

            public function __construct($value)
            {
                $this->value = $value;
            }

            public function process(
                ProcessedValues $processedValues,
                DataStorage $dataStorage
            ): ValidationResult {
                return ValidationResult::finalValueResult($this->value);
            }

            public function updateParamDescription(ParamDescription $paramDescription): void
            {
                //nothing to do.
            }
        };

        $inputParameter = new InputTypeSpec(
            'bar',
            $extractIsFinal
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'bar' => 'hello world'
        ]);

        $paramValues = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataStorage
        );

        $this->assertEmpty($validationProblems);

        $this->assertTrue($paramValues->hasValue('bar'));
        $this->assertSame($value, $paramValues->getValue('bar'));
    }


    /**
     * @covers ::\TypeSpec\processInputParameter
     */
    public function test_processInputParameter_errors()
    {
        $errorMessage = "There was error.";

        $inputParameter = new InputTypeSpec(
            'bar',
            new GetString(),
            new AlwaysErrorsRule($errorMessage)
        );

        $dataStorage = TestArrayDataStorage::fromArray([
            'foo' => 'foo string',
            'bar' => 'bar string'
        ]);

        $paramValues  = new ProcessedValues();
        $validationProblems = processInputParameter(
            $inputParameter,
            $paramValues,
            $dataStorage
        );

        $this->assertValidationProblem(
            '/bar',
            $errorMessage,
            $validationProblems
        );
        $this->assertCount(1, $validationProblems);
    }

    /**
     * @covers ::\TypeSpec\createArrayOfTypeOrError
     */
    public function test_createArrayOfTypeOrError()
    {
        $data = [
            ['limit' => 20],
            ['limit' => 30]
        ];

        [$values, $errors] = createArrayOfTypeOrError(
            FooParams::class,
            $data
        );

        $this->assertEmpty($errors);

        $this->assertCount(2, $values);

        $this->assertInstanceOf(FooParams::class, $values[0]);
        $this->assertInstanceOf(FooParams::class, $values[1]);

        /** @var $fooParam1 FooParams */
        $fooParam1 = $values[0];
        $this->assertSame(20, $fooParam1->getLimit());

        /** @var $fooParam2 FooParams */
        $fooParam2 = $values[1];
        $this->assertSame(30, $fooParam2->getLimit());
    }

    /**
     * @covers ::\TypeSpec\createArrayOfTypeOrError
     */
    public function testErrors_createArrayOfTypeOrError()
    {
        $data = [
            ['limit' => 20],
            ['limit' => -10]
        ];

        [$values, $validationProblems] = createArrayOfTypeOrError(
            FooParams::class,
            $data
        );

        $this->assertNull($values);
        $this->assertValidationErrorCount(1, $validationProblems);

        /** @var \TypeSpec\ValidationProblem[] $validationProblems */
        $validationProblem = $validationProblems[0];

        $this->assertStringMatchesTemplateString(
            Messages::INT_TOO_SMALL,
            $validationProblem->getProblemMessage()
        );
    }


    /**
     * @covers ::\TypeSpec\createArrayOfType
     */
    public function test_createArrayOfType()
    {
        $data = [
            ['limit' => 20],
            ['limit' => 30]
        ];

        $values = createArrayOfType(
            FooParams::class,
            $data
        );

        $this->assertCount(2, $values);

        $this->assertInstanceOf(FooParams::class, $values[0]);
        $this->assertInstanceOf(FooParams::class, $values[1]);

        /** @var $fooParam1 FooParams */
        $fooParam1 = $values[0];
        $this->assertSame(20, $fooParam1->getLimit());

        /** @var $fooParam2 FooParams */
        $fooParam2 = $values[1];
        $this->assertSame(30, $fooParam2->getLimit());
    }


    /**
     * @covers ::\TypeSpec\createArrayOfType
     */
    public function test_createArrayOfTypeErrors()
    {
        $data = [
            ['limit' => 20],
            ['limit' => -30]
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            "Validation problems /[1]/limit Value too small. Min allowed is 0"
        );
        createArrayOfType(
            FooParams::class,
            $data
        );
    }

    /**
     * @covers ::\TypeSpec\checkAllowedFormatsAreStrings
     */
    public function test_checkAllowedFormatsAreStrings()
    {
        $formats = [
            \DateTime::ISO8601,
            \DateTime::RFC2822,
            'D'
        ];

        checkAllowedFormatsAreStrings($formats);

        $bad_formats = [
            \DateTime::ISO8601,
            \DateTime::RFC2822,
            123
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::ERROR_DATE_FORMAT_MUST_BE_STRING
        );

        checkAllowedFormatsAreStrings($bad_formats);
    }

    /**
     * @covers ::\TypeSpec\getInputTypeSpecListFromAnnotations
     */
    public function test_getParamsFromAnnotations()
    {
        $inputParameters = getInputTypeSpecListFromAnnotations(\ThreeColors::class);
        foreach ($inputParameters as $inputParameter) {
            $this->assertInstanceOf(InputTypeSpec::class, $inputParameter);
            $this->assertInstanceOf(GetStringOrDefault::class, $inputParameter->getExtractRule());

            $processRules = $inputParameter->getProcessRules();
            $this->assertCount(1, $processRules);
            $processRule = $processRules[0];
            $this->assertInstanceOf(ImagickIsRgbColor::class, $processRule);
        }
    }


    /**
     * @covers ::\TypeSpec\getInputTypeSpecListFromAnnotations
     */
    public function test_getParamsFromAnnotations_non_existant_param_class()
    {
        try {
            $inputParameters = getInputTypeSpecListFromAnnotations(
                \OneColorWithOtherAnnotationThatDoesNotExist::class
            );
        }
        catch (AnnotationClassDoesNotExistException $acdnee) {
            $this->assertStringContainsString(
                'ThisClassDoesNotExistParam', $acdnee->getMessage()
            );
        }
    }

    /**
     * @covers ::\TypeSpec\getInputTypeSpecListFromAnnotations
     */
    public function testMultipleParamsErrors()
    {
        try {
            $inputParameters = getInputTypeSpecListFromAnnotations(
                \MultipleParamAnnotations::class
            );
        }
        catch (PropertyHasMultipleParamAnnotationsException $acdnee) {
            $this->assertStringContainsString(
                'background_color',
                $acdnee->getMessage()
            );
        }
    }

    /**
     * @covers ::\TypeSpec\getInputTypeSpecListFromAnnotations
     */
    public function test_getParamsFromAnnotations_skips_non_param_annotation()
    {

        $inputParameters = getInputTypeSpecListFromAnnotations(
            \OneColorWithOtherAnnotationThatIsNotAParam::class
        );

        $this->assertCount(1, $inputParameters);
        $inputParameter = $inputParameters[0];

        $this->assertSame('background_color', $inputParameter->getInputName());
    }

    /**
     * @covers ::\TypeSpec\getDefaultSupportedTimeFormats
     */
    public function test_getDefaultSupportedTimeFormats()
    {
        $formats = getDefaultSupportedTimeFormats();
        foreach ($formats as $format) {
            $this->assertIsString($format);
        }
    }

    /**
     * @covers ::\TypeSpec\getReflectionClassOfAttribute
     */
    public function test_getReflectionClassOfAttribute_works()
    {
        $rc = new \ReflectionClass(\ReflectionClassOfAttributeObject::class);

        $refl_property_no_constructor = $rc->getProperty('attribute_exists_no_constructor');
        $attribute = $refl_property_no_constructor->getAttributes()[0];

        $blah = getReflectionClassOfAttribute(
            \ReflectionClassOfAttributeObject::class,
            $attribute->getName(),
            $refl_property_no_constructor
        );

        $this->assertSame(\AttributesExistsNoConstructor::class, $blah->getName());
    }




    /**
     * @covers ::\TypeSpec\getReflectionClassOfAttribute
     */
    public function test_getReflectionClassOfAttribute_attribute_doesnt_exist()
    {
        $rc = new \ReflectionClass(\ReflectionClassOfAttributeObject::class);

        $refl_property_no_constructor = $rc->getProperty('attribute_not_exists');
        $attribute = $refl_property_no_constructor->getAttributes()[0];

        $this->expectException(AnnotationClassDoesNotExistException::class);
        getReflectionClassOfAttribute(
            \ReflectionClassOfAttributeObject::class,
            $attribute->getName(),
            $refl_property_no_constructor
        );
    }




//    /**
//     * @covers ::\TypeSpec\instantiateParam
//     */
//    public function test_instantiateParam_works()
//    {
//        $rc = new \ReflectionClass(\ReflectionClassOfAttributeObject::class);
//
//        $refl_property_no_constructor = $rc->getProperty('attribute_exists_no_constructor');
//        $attribute = $refl_property_no_constructor->getAttributes()[0];
//
//        $refl_of_attribute = new \ReflectionClass($attribute->getName());
//
//        $result = instantiateParam(
//            $refl_of_attribute,
//            $attribute,
//            'default_name',
//        );
//        $this->assertInstanceOf(\AttributesExistsNoConstructor::class, $result);
//    }


//    /**
//     * @covers ::\TypeSpec\instantiateParam
//     */
//    public function test_instantiateParam_works_param_but_no_name()
//    {
//        $rc = new \ReflectionClass(\ReflectionClassOfAttributeObject::class);
//
//        $refl_property_no_constructor = $rc->getProperty('attribute_exists_has_constructor');
//        $attribute = $refl_property_no_constructor->getAttributes()[0];
//
//        $refl_of_attribute = new \ReflectionClass($attribute->getName());
//
//        $result = instantiateParam(
//            $refl_of_attribute,
//            $attribute,
//            'default_name',
//        );
//        $this->assertInstanceOf(\AttributesExistsHasConstructor::class, $result);
//        $this->assertSame(10, $result->getFoo());
//    }

//    /**
//     * @covers ::\TypeSpec\instantiateParam
//     */
//    public function test_instantiateParam_works_param_with_name()
//    {
//        $rc = new \ReflectionClass(\ReflectionClassOfAttributeObject::class);
//
//        $refl_property_no_constructor = $rc->getProperty('attribute_exists_has_constructor_with_name');
//        $attribute = $refl_property_no_constructor->getAttributes()[0];
//
//        $refl_of_attribute = new \ReflectionClass($attribute->getName());
//
//        $result = instantiateParam(
//            $refl_of_attribute,
//            $attribute,
//            'default_name'
//        );
//        $this->assertInstanceOf(\AttributesExistsHasConstructorWithName::class, $result);
//        $this->assertSame(10, $result->getFoo());
//
//        $this->assertSame('default_name', $result->getName());
//    }
}
