<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\IncorrectNumberOfParamsException;

/**
 * @coversNothing
 */
class IncorrectNumberOfParamsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \TypeSpec\Exception\IncorrectNumberOfParamsException
     */
    public function testWorks()
    {
        $exception = IncorrectNumberOfParamsException::wrongNumber(
            self::class,
            3,
            4
        );

        $expected_message = sprintf(
            Messages::INCORRECT_NUMBER_OF_PARAMS,
            self::class,
            3,
            4
        );

        $this->assertSame(
            $expected_message,
            $exception->getMessage()
        );
    }
}
