<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\MissingClassException;

/**
 * @coversNothing
 */
class MissingClassExceptionTest extends BaseTestCase
{

    /**
     * @covers \TypeSpec\Exception\MissingClassException
     */
    public function testInputParameterListException()
    {
        $exception = MissingClassException::fromClassname(self::class);
        $this->assertStringMatchesTemplateString(
            Messages::CLASS_NOT_FOUND,
            $exception->getMessage()
        );

        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }


//TypeNotInputParameterListException.php
}
