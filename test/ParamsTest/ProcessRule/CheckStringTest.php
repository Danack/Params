<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\Exception\InvalidRulesException;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\CheckString;
use Type\Messages;

/**
 * @coversNothing
 */
class CheckStringTest extends BaseTestCase
{
    /**
     * @covers \Type\ProcessRule\CheckString
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
     * @covers \Type\ProcessRule\CheckString
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
