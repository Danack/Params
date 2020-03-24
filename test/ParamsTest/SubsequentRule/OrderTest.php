<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\Order;
use Params\Value\Ordering;
use Params\ParamsValuesImpl;

/**
 * @coversNothing
 */
class OrderTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            ['time', ['time' => Ordering::ASC], false],
            ['bar', null, true],
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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process('foo', $testValue, $validator);

        if ($expectError === true) {
            $this->assertNotNull($validationResult->getValidationProblems());
            return;
        }

        $value = $validationResult->getValue();
        $this->assertInstanceOf(Ordering::class, $value);
        /** @var $value Ordering */
        $this->assertEquals($expectedOrdering, $value->toOrderArray());
    }
}
