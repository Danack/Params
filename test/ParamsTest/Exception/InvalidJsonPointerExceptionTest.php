<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\InvalidJsonPointerException;

/**
 * @coversNothing
 */
class InvalidJsonPointerExceptionTest extends BaseTestCase
{
    /**
     * @covers \Params\Exception\InvalidJsonPointerException
     */
    public function testInputParameterListException_notArray()
    {
        $exception = InvalidJsonPointerException::invalidFirstCharacter();
        $this->assertSame(
            Messages::INVALID_JSON_POINTER_FIRST,
            $exception->getMessage()
        );

        $this->assertSame(0, $exception->getCode());
    }
}
