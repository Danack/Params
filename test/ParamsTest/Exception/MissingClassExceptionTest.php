<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\Exception\MissingClassException;

/**
 * @coversNothing
 */
class MissingClassExceptionTest extends BaseTestCase
{

    /**
     * @covers \Type\Exception\MissingClassException
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
