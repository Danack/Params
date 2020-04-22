<?php

declare(strict_types=1);

namespace ParamsTest;

use Params\DataLocator\DataStorage;
use Params\ValidationProblem;
use ParamsTest\BaseTestCase;
use Params\ValidationResult;
use Params\Exception\LogicException;

/**
 * @covers \Params\ValidationResult
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
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $dataLocatorForPath = $dataLocator->moveKey($path);

        $validationMessage = 'Something went wrong';
        $validationResult = ValidationResult::errorResult(
            $dataLocatorForPath,
            $validationMessage
        );

        $this->assertTrue($validationResult->isFinalResult());
        $this->assertNull($validationResult->getValue());

        $problems = $validationResult->getValidationProblems();
        $this->assertCount(1, $problems);
        $firstProblem = $problems[0];
        $this->assertSame('/foo', $firstProblem->getDataLocator()->getPath());
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
        $dataStorage = DataStorage::fromArray([]);

        $key = 'nonexistent';

        $dataStorage = $dataStorage->moveKey($key);
        $problemMessage = 'There was problem';

        $validationProblem = new ValidationProblem($dataStorage, $problemMessage);
        $validationResult = ValidationResult::fromValidationProblems([$validationProblem]);
        $this->assertSame([$validationProblem], $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->anyErrorsFound());
    }

    public function testFromValidationProblemsBadKey()
    {
        $dataStorage = DataStorage::fromArray([]);

        $key = 'nonexistent';

        $dataStorage = $dataStorage->moveKey($key);
        $problemMessage = 'There was problem';

        $validationProblem = new ValidationProblem($dataStorage, $problemMessage);
        $this->expectExceptionMessageMatchesRegexp(LogicException::ONLY_INT_KEYS);
        $this->expectException(LogicException::class);
        $validationResult = ValidationResult::fromValidationProblems(['foo' => $validationProblem]);
    }

    public function testFromValidationProblemsNotInputParameter()
    {
        $this->expectExceptionMessageMatchesRegexp(LogicException::NOT_VALIDATION_PROBLEM);
        $this->expectException(LogicException::class);
        $validationResult = ValidationResult::fromValidationProblems([new \StdClass()]);
    }
}
