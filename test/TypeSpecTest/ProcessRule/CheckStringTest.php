<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\Exception\InvalidRulesException;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\CheckString;
use TypeSpec\Messages;

/**
 * @coversNothing
 */
class CheckStringTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessRule\CheckString
     */
    public function testWorks()
    {
        $obj = new class {
            use CheckString;
        };

        $result = $obj->checkString("foo");
        $this->assertIsString($result);

        $this->expectException(InvalidRulesException::class);
        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE
        );
        $obj->checkString(5);
    }

    /**
     * @covers \TypeSpec\ProcessRule\CheckString
     */
    public function testStdClassFails()
    {
        $obj = new class {
            use CheckString;
        };

        $inputString = "foo";

        $someString = new class($inputString) implements \stringable {
            public function __construct(private string $name)
            {
            }

            public function __toString()
            {
                return $this->name;
            }
        };


        $result = $obj->checkString($someString);
        $this->assertSame($inputString, $result);
    }
}
