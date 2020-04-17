<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Value\Ordering;
use function Params\createPath;
use function Params\unescapeJsonPointer;
use function Params\array_value_exists;
use function Params\check_only_digits;
use function Params\normalise_order_parameter;
use function Params\escapeJsonPointer;

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
     * @covers ::Params\normalise_order_parameter
     */
    public function testNormaliseOrderParameter($input, $expectedName, $expectedOrder)
    {
        list($name, $order) = normalise_order_parameter($input);

        $this->assertEquals($expectedName, $name);
        $this->assertEquals($expectedOrder, $order);
    }

    /**
     * @covers ::Params\check_only_digits
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
     * @covers ::Params\array_value_exists
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
     * @covers ::\Params\escapeJsonPointer
     */
    public function testEscapeJsonPointer($unescaped, $expectedEscaped)
    {
        $actualEscaped = escapeJsonPointer($unescaped);
        $this->assertSame($expectedEscaped, $actualEscaped);
    }

    /**
     * @dataProvider providesEscapeJsonPointer
     * @covers ::Params\unescapeJsonPointer
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

    public function providesCreatePath()
    {
        yield ['/', []];
        yield ['/foo', ['name' => 'foo']];
        yield ['/[3]', ['index' => 3]];
        yield ['/foo/[3]', ['name' => 'foo', 'index' => 3]];
    }


    /**
     * @dataProvider providesCreatePath()
     * @param array $pathParts
     * @param string $expectedOutput
     * @group new
     */
    public function testCreatePath(string $expectedOutput, array $pathParts)
    {
        $actual = createPath($pathParts);
        $this->assertSame($expectedOutput, $actual);
    }




}
