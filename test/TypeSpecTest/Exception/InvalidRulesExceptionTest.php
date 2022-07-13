<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\InvalidRulesException;

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
     * @covers \TypeSpec\Exception\InvalidRulesException
     * @dataProvider providesInvalidRulesException
     */
    public function testInvalidRulesException($badValue, $badTypeString)
    {
        $exception = InvalidRulesException::badTypeForArrayAccess($badValue);
        $this->assertStringMatchesTemplateString(
            Messages::BAD_TYPE_FOR_ARRAY_ACCESS,
            $exception->getMessage()
        );

        $this->assertStringContainsString($badTypeString, $exception->getMessage());

        $this->assertSame(0, $exception->getCode());
    }

    /**
     * @covers \TypeSpec\Exception\InvalidRulesException
     */
    public function testExpectsStringForProcessing()
    {
        $exception = InvalidRulesException::expectsStringForProcessing('some_class_name');
        $this->assertStringMatchesTemplateString(
            Messages::BAD_TYPE_FOR_STRING_PROCESS_RULE,
            $exception->getMessage()
        );

        $this->assertStringContainsString('some_class_name', $exception->getMessage());

        $this->assertSame(0, $exception->getCode());
    }
}
