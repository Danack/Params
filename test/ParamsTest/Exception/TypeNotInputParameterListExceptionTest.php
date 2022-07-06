<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Type\Type;
use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\Exception\TypeNotInputParameterListException;

/**
 * @coversNothing
 */
class TypeNotInputParameterListExceptionTest extends BaseTestCase
{
    /**
     * @covers \Type\Exception\TypeNotInputParameterListException
     */
    public function testInputParameterListException()
    {
        $exception = TypeNotInputParameterListException::fromClassname(self::class);
        $this->assertStringMatchesTemplateString(
            Messages::CLASS_MUST_IMPLEMENT_INPUT_PARAMETER,
            $exception->getMessage()
        );

        // This should survive class renaming.
        $this->assertStringContainsString(Type::class, $exception->getMessage());
        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
