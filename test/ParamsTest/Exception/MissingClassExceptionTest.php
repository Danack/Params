<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\MissingClassException;

/**
 * @coversNothing
 */
class MissingClassExceptionTest extends BaseTestCase
{

    /**
     * @covers \Params\Exception\MissingClassException
     */
    public function testInputParameterListException()
    {
        $exception = MissingClassException::fromClassname(self::class);
        $this->assertStringRegExp(
            Messages::CLASS_NOT_FOUND,
            $exception->getMessage()
        );


        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }


//TypeNotInputParameterListException.php
}
