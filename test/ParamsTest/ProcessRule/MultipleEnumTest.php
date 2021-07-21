<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\Order;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MultipleEnum;
use Params\Value\MultipleEnums;
use Params\ProcessedValues;
use Params\Messages;

/**
 * @coversNothing
 */
class MultipleEnumTest extends BaseTestCase
{

    public function providesMultipleEnumWorks()
    {
        return [
            ['foo', ['foo']],
            ['bar,foo', ['bar', 'foo']],
        ];
    }

    /**
     * @dataProvider providesMultipleEnumWorks
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testMultipleEnumWorks($inputString, $expectedResult)
    {
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputString, $processedValues, $dataLocator
        );
        $this->assertNoProblems($validationResult);

        $validationValue = $validationResult->getValue();

        $this->assertInstanceOf(MultipleEnums::class, $validationValue);
        /** @var $validationValue \Params\Value\MultipleEnums */

        $this->assertEquals($expectedResult, $validationValue->getValues());
    }

    /**
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testMultipleEnumErrors()
    {
        $badValue = 'zot';
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $badValue,
            $processedValues,
            ArrayInputStorage::fromSingleValue('foo', $badValue)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ENUM_MAP_UNRECOGNISED_VALUE_MULTIPLE,
            $validationResult->getValidationProblems()
        );
    }

    public function provideMultipleEnumCases()
    {
        return [
            ['foo,', ['foo']],
            [',,,,,foo,', ['foo']],
        ];
    }

    /**
     * @dataProvider provideMultipleEnumCases
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testMultipleEnum_emptySegments($input, $expectedOutput)
    {
        $enumRule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $result = $enumRule->process(
            $input, $processedValues, $dataLocator
        );

        $this->assertEmpty($result->getValidationProblems());
        $value = $result->getValue();
        $this->assertInstanceOf(MultipleEnums::class, $value);
        $this->assertEquals($expectedOutput, $value->getValues());
    }

    // TODO - these appear to be duplicate tests.
    public function provideTestCases()
    {
        return [
            ['time', ['time'], false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testValidation($testValue, $expectedMultipleEnumValues, $expectError)
    {
        $rule = new MultipleEnum(['time', 'distance']);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        $value = $validationResult->getValue();
        $this->assertInstanceOf(MultipleEnums::class, $value);

        /** @var $value \Params\Value\MultipleEnums */
        $this->assertEquals($expectedMultipleEnumValues, $value->getValues());
    }


    public function provideTestErrors()
    {
        yield ['bar'];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testErrors($testValue)
    {
        $values = ['time', 'distance'];

        $rule = new MultipleEnum($values);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ENUM_MAP_UNRECOGNISED_VALUE_MULTIPLE,
            $validationResult->getValidationProblems()
        );
        $this->assertOneErrorAndContainsString(
            $validationResult,
            implode(", ", $values)
        );
    }

    /**
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testDescription()
    {
        $values = ['time', 'distance'];

        $rule = new MultipleEnum($values);
        $description = new OpenApiV300ParamDescription('John');
        $rule->updateParamDescription($description);
    }
}
