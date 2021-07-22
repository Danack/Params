<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\NoConstructorException;

/**
 * @coversNothing
 */
class NoConstructorExceptionTest extends BaseTestCase
{
    /**
     * This test seems dumb.
     * @covers \Params\Exception\NoConstructorException
     */
    public function testNoConstructorWorks()
    {
        $exception = NoConstructorException::noConstructor(
            'John'
        );

        $this->assertStringMatchesTemplateString(
            Messages::CLASS_LACKS_CONSTRUCTOR,
            $exception->getMessage()
        );
        $this->assertStringContainsString('John', $exception->getMessage());
    }

    /**
     * This test seems dumb.
     * @covers \Params\Exception\NoConstructorException
     */
    public function testnotPublicConstructor()
    {
        $exception = NoConstructorException::notPublicConstructor(
            'John'
        );

        $this->assertStringMatchesTemplateString(
            Messages::CLASS_LACKS_PUBLIC_CONSTRUCTOR,
            $exception->getMessage()
        );
        $this->assertStringContainsString('John', $exception->getMessage());
    }
}
