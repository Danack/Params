<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\Exception\InvalidRulesException;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\CheckString;
use Params\Messages;

/**
 * @coversNothing
 */
class CheckStringTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\CheckString
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
}
