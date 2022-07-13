<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\InvalidDatetimeFormatException;

/**
 * @coversNothing
 */
class InvalidDatetimeFormatExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \TypeSpec\Exception\InvalidDatetimeFormatException
     */
    public function testWorks()
    {
        $exception = InvalidDatetimeFormatException::stringRequired(
            4, []
        );

        $this->assertStringMatchesTemplateString(
            Messages::ERROR_DATE_FORMAT_MUST_BE_STRING,
            $exception->getMessage()
        );
        $this->assertStringContainsString('array', $exception->getMessage());
    }
}
