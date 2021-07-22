<?php

declare(strict_types=1);

namespace ParamsTest\Exception;

use Params\InputStorage\ArrayInputStorage;
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
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);

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

    public function testMessageIsCorrect()
    {
        $detail_of_problem = "";
        $general_description = 'General description';

        $dataLocator = ArrayInputStorage::fromArray(['foo' => 'bar']);
        $dataLocatorAtFoo = $dataLocator->moveKey('foo');

        $validationProblem = new ValidationProblem($dataLocatorAtFoo, $detail_of_problem);

        $exception = new ValidationException(
            $general_description,
            [$validationProblem]
        );

        $message = $exception->getMessage();

        $this->assertStringStartsWith($general_description . " ", $message);
        $this->assertStringContainsString($detail_of_problem, $message);
    }
}
