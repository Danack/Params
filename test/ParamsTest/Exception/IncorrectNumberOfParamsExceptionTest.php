<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\IncorrectNumberOfParamsException;

/**
 * @coversNothing
 */
class IncorrectNumberOfParamsExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \Params\Exception\IncorrectNumberOfParamsException
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
