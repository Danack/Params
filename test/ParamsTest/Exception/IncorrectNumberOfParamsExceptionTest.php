<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\Exception\IncorrectNumberOfParamsException;

/**
 * @coversNothing
 */
class IncorrectNumberOfParamsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \Type\Exception\IncorrectNumberOfParamsException
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
