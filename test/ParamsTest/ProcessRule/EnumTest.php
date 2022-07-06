<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use Type\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\Enum;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class EnumTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['zoq',  'zoq'],
            ['12345', '12345'],

//            ['Zebranky ', true, null],
//            [12345, true, null]
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Type\ProcessRule\Enum
     */
    public function testWorks($testValue, $expectedValue)
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];

        $rule = new Enum($enumValues);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrors()
    {
        yield ['Zebranky '];
        yield [12345, ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Type\ProcessRule\Enum
     */
    public function testValidationErrors($testValue)
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];

        $rule = new Enum($enumValues);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);
        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ENUM_MAP_UNRECOGNISED_VALUE_SINGLE,
            $validationResult->getValidationProblems()
        );
    }


    /**
     * @covers \Type\ProcessRule\Enum
     */
    public function testDescription()
    {
        $enumValues = ['zoq', 'fot', 'pik', '12345'];
        $rule = new Enum($enumValues);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame($enumValues, $description->getEnumValues());
    }
}
