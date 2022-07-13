<?php

declare(strict_types=1);

namespace TypeSpecTest\Exception;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpecTest\BaseTestCase;
use TypeSpec\Exception\ValidationException;
use TypeSpec\ValidationProblem;

/**
 * @coversNothing
 */
class ValidationExceptionTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\Exception\ValidationException
     */
    public function testGetting()
    {
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);

        $message1 = 'foo was invalid';
        $message2 = 'bar was invalid';

        $validationMessages = [
            new ValidationProblem($dataStorage, $message1),
            new ValidationProblem($dataStorage, $message2)
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

        $dataStorage = TestArrayDataStorage::fromArray(['foo' => 'bar']);
        $dataStorageAtFoo = $dataStorage->moveKey('foo');

        $validationProblem = new ValidationProblem($dataStorageAtFoo, $detail_of_problem);

        $exception = new ValidationException(
            $general_description,
            [$validationProblem]
        );

        $message = $exception->getMessage();

        $this->assertStringStartsWith($general_description . " ", $message);
        $this->assertStringContainsString($detail_of_problem, $message);
    }
}
