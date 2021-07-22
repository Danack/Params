<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\InvalidDatetimeFormatException;

/**
 * @coversNothing
 */
class InvalidDatetimeFormatExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \Params\Exception\InvalidDatetimeFormatException
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
