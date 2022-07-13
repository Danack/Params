<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception\Validator;

use TypeSpecTest\BaseTestCase;
use TypeSpec\Value\OrderElement;
use TypeSpec\Value\Ordering;

/**
 * @coversNothing
 */
class OrderingTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Value\OrderElement
     * @covers \TypeSpec\Value\Ordering
     */
    public function testBasic()
    {
        $name = 'foo';
        $order = 'asc';

        $orderElment = new OrderElement($name, $order);
        $this->assertEquals($name, $orderElment->getName());
        $this->assertEquals($order, $orderElment->getOrder());

        $ordering = new Ordering([$orderElment]);
        $this->assertEquals([$orderElment], $ordering->getOrderElements());

        $expectedOrderArray = [
            $name => $order
        ];

        $this->assertEquals($expectedOrderArray, $ordering->toOrderArray());
    }
}
