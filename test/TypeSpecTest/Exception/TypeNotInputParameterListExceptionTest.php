<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\TypeSpec;
use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\TypeNotInputParameterListException;

/**
 * @coversNothing
 */
class TypeNotInputParameterListExceptionTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Exception\TypeNotInputParameterListException
     */
    public function testInputParameterListException()
    {
        $exception = TypeNotInputParameterListException::fromClassname(self::class);
        $this->assertStringMatchesTemplateString(
            Messages::CLASS_MUST_IMPLEMENT_INPUT_PARAMETER,
            $exception->getMessage()
        );

        // This should survive class renaming.
        $this->assertStringContainsString(TypeSpec::class, $exception->getMessage());
        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
