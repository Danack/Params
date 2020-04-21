<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\Exception\ValidationException;
use Params\ValidationProblem;

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
        $dataLocator = DataStorage::fromArraySetFirstValue([]);

        $message1 = 'foo was invalid';
        $message2 = 'bar was invalid';

        $validationMessages = [
            new ValidationProblem($dataLocator, $message1),
            new ValidationProblem($dataLocator, $message2)
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

        $strings = $exception->getValidationProblemsAsStrings();
        $actualStrings = [
            '/ ' . $message1,
            '/ ' . $message2
        ];
        $this->assertSame($actualStrings, $strings);
    }
}
