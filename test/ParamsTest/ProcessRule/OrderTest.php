<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataStorage\TestArrayDataStorage;
use Params\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\Order;
use Params\Value\Ordering;
use Params\ProcessedValues;
use Params\Messages;
use Params\OpenApi\ParamDescription;

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
     * @covers \Params\ProcessRule\Order
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
     * @covers \Params\ProcessRule\Order
     */
    public function testErrors($testValue, $expectedOrdering, $expectError)
    {
        $orderParams = ['time', 'distance'];

        $rule = new Order($orderParams);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

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
     * @covers \Params\ProcessRule\Order
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
