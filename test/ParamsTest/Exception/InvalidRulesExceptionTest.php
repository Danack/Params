<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\InvalidRulesException;

/**
 * @coversNothing
 */
class InvalidRulesExceptionTest extends BaseTestCase
{

    public function providesInvalidRulesException()
    {
        yield [new \StdClass(), 'object'];
        yield [[], 'array'];
        yield [4.3, 'double'];
    }

    /**
     * @covers \Params\Exception\InvalidRulesException
     * @dataProvider providesInvalidRulesException
     */
    public function testInvalidRulesException($badValue, $badTypeString)
    {
        $exception = InvalidRulesException::badTypeForArrayAccess($badValue);
        $this->assertStringRegExp(
            Messages::BAD_TYPE_FOR_ARRAY_ACCESS,
            $exception->getMessage()
        );

        $this->assertStringContainsString($badTypeString, $exception->getMessage());

        $this->assertSame(0, $exception->getCode());
    }


//MissingClassException.php
//TypeNotInputParameterListException.php
}
