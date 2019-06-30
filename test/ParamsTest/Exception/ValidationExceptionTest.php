<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;
use Params\ValidationErrors;

/**
 * @covers \Params\Exception\ValidationException
 */
class ValidationExceptionTest extends BaseTestCase
{
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
    }
}
