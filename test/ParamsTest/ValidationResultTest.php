<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ValidationResult;

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
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
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
    }

    public function testFinalValueResult()
    {
        $value = 5;
        $validationResult = ValidationResult::finalValueResult($value);
        $this->assertTrue($validationResult->isFinalResult());
        $this->assertEquals($value, $validationResult->getValue());
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
    }
}
