<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;
use Params\Functions;
use Params\Value\Ordering;

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
     * @covers \Params\Functions::normalise_order_parameter
     */
    public function testNormaliseOrderParameter($input, $expectedName, $expectedOrder)
    {
        list($name, $order) = Functions::normalise_order_parameter($input);

        $this->assertEquals($expectedName, $name);
        $this->assertEquals($expectedOrder, $order);
    }

    /**
     * @covers \Params\Functions::check_only_digits
     */
    public function testCheckOnlyDigits()
    {
        // An integer gets short circuited
        $errorMsg = Functions::check_only_digits('Foo', 12345);
        $this->assertNull($errorMsg);

        // Correct string passes through
        $errorMsg = Functions::check_only_digits('Foo', '12345');
        $this->assertNull($errorMsg);

        // Incorrect string passes through
        $errorMsg = Functions::check_only_digits('Foo', '123X45');
        $this->assertStringMatchesFormat("%sposition 3%s", $errorMsg);
        $this->assertStringMatchesFormat("%sFoo%s", $errorMsg);
    }

    /**
     * @covers \Params\Functions::array_value_exists
     */
    public function testArrayValueExists()
    {
        $values = [
            '1',
            '2',
            '3'
        ];

        $foundExactType = Functions::array_value_exists($values, '2');
        $this->assertTrue($foundExactType);

        $foundJuggledType = Functions::array_value_exists($values, 2);
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
     * @covers \Params\Functions::escapeJsonPointer
     */
    public function testEscapeJsonPointer($unescaped, $expectedEscaped)
    {
        $actualEscaped = Functions::escapeJsonPointer($unescaped);
        $this->assertSame($expectedEscaped, $actualEscaped);
    }

    /**
     * @dataProvider providesEscapeJsonPointer
     * @covers \Params\Functions::unescapeJsonPointer
     */
    public function testUnescapeJsonPointer($expectedUnescaped, $escaped)
    {
        $actualUnescaped = Functions::unescapeJsonPointer($escaped);
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
}
