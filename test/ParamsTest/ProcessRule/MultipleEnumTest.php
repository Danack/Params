<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\OpenApi\OpenApiV300ParamDescription;
use Type\ProcessRule\Order;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\MultipleEnum;
use Type\Value\MultipleEnums;
use Type\ProcessedValues;
use Type\Messages;

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
     * @covers \Type\ProcessRule\MultipleEnum
     */
    public function testMultipleEnumWorks($inputString, $expectedResult)
    {
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $inputString, $processedValues, $dataStorage
        );
        $this->assertNoProblems($validationResult);

        $validationValue = $validationResult->getValue();

        $this->assertInstanceOf(MultipleEnums::class, $validationValue);
        /** @var $validationValue \Type\Value\MultipleEnums */

        $this->assertEquals($expectedResult, $validationValue->getValues());
    }

    /**
     * @covers \Type\ProcessRule\MultipleEnum
     */
    public function testMultipleEnumErrors()
    {
        $badValue = 'zot';
        $rule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $badValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValue('foo', $badValue)
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
     * @covers \Type\ProcessRule\MultipleEnum
     */
    public function testMultipleEnum_emptySegments($input, $expectedOutput)
    {
        $enumRule = new MultipleEnum(['foo', 'bar']);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $result = $enumRule->process(
            $input, $processedValues, $dataStorage
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
     * @covers \Type\ProcessRule\MultipleEnum
     */
    public function testValidation($testValue, $expectedMultipleEnumValues, $expectError)
    {
        $rule = new MultipleEnum(['time', 'distance']);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $value = $validationResult->getValue();
        $this->assertInstanceOf(MultipleEnums::class, $value);

        /** @var $value \Type\Value\MultipleEnums */
        $this->assertEquals($expectedMultipleEnumValues, $value->getValues());
    }


    public function provideTestErrors()
    {
        yield ['bar'];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Type\ProcessRule\MultipleEnum
     */
    public function testErrors($testValue)
    {
        $values = ['time', 'distance'];

        $rule = new MultipleEnum($values);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
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
     * @covers \Type\ProcessRule\MultipleEnum
     */
    public function testDescription()
    {
        $values = ['time', 'distance'];

        $rule = new MultipleEnum($values);
        $description = $this->applyRuleToDescription($rule);
    }
}
