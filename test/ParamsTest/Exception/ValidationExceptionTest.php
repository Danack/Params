<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;
use Params\ValidationErrors;
use Params\Exception\ParamsException;

/**
 * @coversNothing
 */
class ValidationExceptionTest extends BaseTestCase
{
    /**
     * @covers \Params\Exception\ValidationException
     */
    public function testGetting()
    {
        $validationMessages = [
            'foo',
            'bar'
        ];

        $exception = new ValidationException(
            'unit test',
            $validationMessages
        );

        $this->assertEquals(
            $validationMessages,
            $exception->getValidationProblems()
        );

        $this->assertSame(0, $exception->getCode());
    }


    /**
     * @covers \Params\Exception\ParamsException
     */
    public function testParamsException()
    {
        $exception = ParamsException::badFirstRule();

        $this->assertSame(
            ParamsException::ERROR_FIRST_RULE_MUST_IMPLEMENT_FIRST_RULE,
            $exception->getMessage()
        );
        $this->assertInstanceOf(ParamsException::class, $exception);
    }
}
