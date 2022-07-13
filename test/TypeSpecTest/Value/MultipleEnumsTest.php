<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception\Validator;

use TypeSpecTest\BaseTestCase;
use TypeSpec\Value\MultipleEnums;

/**
 * @coversNothing
 */
class MultipleEnumsTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Value\MultipleEnums
     */
    public function testBasic()
    {
        $values = [
            'foo',
            'bar'
        ];

        $multipleEnums = new MultipleEnums($values);

        $this->assertEquals($values, $multipleEnums->getValues());
    }
}
