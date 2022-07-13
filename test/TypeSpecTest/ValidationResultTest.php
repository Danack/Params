<?php

declare(strict_types=1);

namespace TypeSpecTest;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\ValidationProblem;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ValidationResult;
use TypeSpec\Exception\LogicException;

/**
 * @covers \TypeSpec\ValidationResult
 */
class ValidationResultTest extends BaseTestCase
{
    public function testValueResult()
    {
        $value = 5;
        $validationResult = ValidationResult::valueResult($value);
        $this->assertFalse($validationResult->isFinalResult());
        $this->assertEquals($value, $validationResult->getValue());
        $this->assertNoProblems($validationResult);
    }

    public function testErrorResult()
    {
        $path = 'foo';
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $dataStorageForPath = $dataStorage->moveKey($path);

        $validationMessage = 'Something went wrong';
        $validationResult = ValidationResult::errorResult(
            $dataStorageForPath,
            $validationMessage
        );

        $this->assertTrue($validationResult->isFinalResult());
        $this->assertNull($validationResult->getValue());

        $problems = $validationResult->getValidationProblems();
        $this->assertCount(1, $problems);
        $firstProblem = $problems[0];
        $this->assertSame('/foo', $firstProblem->getInputStorage()->getPath());
        $this->assertEquals($validationMessage, $firstProblem->getProblemMessage());

        $this->assertTrue($validationResult->anyErrorsFound());
    }

    public function testFinalValueResult()
    {
        $value = 5;
        $validationResult = ValidationResult::finalValueResult($value);
        $this->assertTrue($validationResult->isFinalResult());
        $this->assertEquals($value, $validationResult->getValue());
        $this->assertNoProblems($validationResult);

        $this->assertFalse($validationResult->anyErrorsFound());
    }

    public function testFromValidationProblemsWorks()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $key = 'nonexistent';

        $dataStorage = $dataStorage->moveKey($key);
        $problemMessage = 'There was problem';

        $validationProblem = new ValidationProblem($dataStorage, $problemMessage);
        $validationResult = ValidationResult::fromValidationProblems([$validationProblem]);
        $this->assertSame([$validationProblem], $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->anyErrorsFound());
        $this->assertTrue($validationResult->isFinalResult());
    }

    public function testFromValidationProblemsBadKey()
    {
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $key = 'nonexistent';

        $dataStorage = $dataStorage->moveKey($key);
        $problemMessage = 'There was problem';

        $validationProblem = new ValidationProblem($dataStorage, $problemMessage);
        $this->expectExceptionMessageMatchesTemplateString(LogicException::ONLY_INT_KEYS);
        $this->expectException(LogicException::class);
        $validationResult = ValidationResult::fromValidationProblems(['foo' => $validationProblem]);
    }

    public function testFromValidationProblemsNotInputParameter()
    {
        $this->expectExceptionMessageMatchesTemplateString(LogicException::NOT_VALIDATION_PROBLEM);
        $this->expectException(LogicException::class);
        $validationResult = ValidationResult::fromValidationProblems([new \StdClass()]);
    }
}
