<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;
use Params\Exception\ParamsException;
use Params\ValidationProblem;
use Params\Path;

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
            new ValidationProblem(Path::fromName('foo'), 'foo was invalid'),
            new ValidationProblem(Path::fromName('bar'), 'bar was invalid')
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


//    /**
//     * @covers \Params\Exception\ParamsException
//     */
//    public function testParamsException()
//    {
//        $exception = ParamsException::badFirstRule();
//
//        $this->assertSame(
//            ParamsException::ERROR_FIRST_RULE_MUST_IMPLEMENT_FIRST_RULE,
//            $exception->getMessage()
//        );
//        $this->assertInstanceOf(ParamsException::class, $exception);
//    }
}
