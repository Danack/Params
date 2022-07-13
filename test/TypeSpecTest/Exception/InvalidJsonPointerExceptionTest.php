<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\InvalidJsonPointerException;

/**
 * @coversNothing
 */
class InvalidJsonPointerExceptionTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Exception\InvalidJsonPointerException
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
