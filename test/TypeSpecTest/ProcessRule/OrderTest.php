<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\Order;
use TypeSpec\Value\Ordering;
use TypeSpec\ProcessedValues;
use TypeSpec\Messages;
use TypeSpec\OpenApi\ParamDescription;

/**
 * @coversNothing
 */
class OrderTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['time', ['time' => Ordering::ASC], false],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \TypeSpec\ProcessRule\Order
     */
    public function testValidation($testValue, $expectedOrdering, $expectError)
    {
        $orderParams = ['time', 'distance'];

        $rule = new Order($orderParams);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $value = $validationResult->getValue();
        $this->assertInstanceOf(Ordering::class, $value);
        /** @var $value Ordering */
        $this->assertEquals($expectedOrdering, $value->toOrderArray());
    }

    public function provideTestErrors()
    {
        return [
            ['bar', null, true],
        ];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \TypeSpec\ProcessRule\Order
     */
    public function testErrors($testValue, $expectedOrdering, $expectError)
    {
        $orderParams = ['time', 'distance'];

        $rule = new Order($orderParams);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ORDER_VALUE_UNKNOWN,
            $validationResult->getValidationProblems()
        );

        $this->assertOneErrorAndContainsString(
            $validationResult,
            implode(", ", $orderParams)
        );
    }

    /**
     * @covers \TypeSpec\ProcessRule\Order
     */
    public function testDescription()
    {
        $orderParams = ['time', 'distance'];
        $rule = new Order($orderParams);
        $description = $this->applyRuleToDescription($rule);

        $this->assertSame(
            ParamDescription::COLLECTION_CSV,
            $description->getCollectionFormat()
        );
    }
}
