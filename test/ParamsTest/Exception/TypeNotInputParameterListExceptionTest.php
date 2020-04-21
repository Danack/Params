<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\InputParameterList;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\Exception\TypeNotInputParameterListException;

/**
 * @coversNothing
 */
class TypeNotInputParameterListExceptionTest extends BaseTestCase
{
    /**
     * @covers \Params\Exception\TypeNotInputParameterListException
     */
    public function testInputParameterListException()
    {
        $exception = TypeNotInputParameterListException::fromClassname(self::class);
        $this->assertStringRegExp(
            Messages::CLASS_MUST_IMPLEMENT_INPUT_PARAMETER,
            $exception->getMessage()
        );

        // This should survive class renaming.
        $this->assertStringContainsString(InputParameterList::class, $exception->getMessage());
        $this->assertStringContainsString(self::class, $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }
}
