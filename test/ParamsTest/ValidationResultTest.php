<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

use ParamsTest\BaseTestCase;
use Params\ValidationResult;
use Params\Path;

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
        $this->assertEmpty($validationResult->getValidationProblems());
    }

    public function testErrorResult()
    {
        $name = 'foo';
        $path = Path::fromName($name);

        $validationMessage = 'Something went wrong';
        $validationResult = ValidationResult::errorResult(
            $path,
            $validationMessage
        );

        $this->assertTrue($validationResult->isFinalResult());
        $this->assertNull($validationResult->getValue());

        $problems = $validationResult->getValidationProblems();
        $this->assertCount(1, $problems);
        $firstProblem = $problems[0];
        $this->assertSame($path, $firstProblem->getPath());
        $this->assertEquals($validationMessage, $firstProblem->getProblemMessage());
    }

    public function testFinalValueResult()
    {
        $value = 5;
        $validationResult = ValidationResult::finalValueResult($value);
        $this->assertTrue($validationResult->isFinalResult());
        $this->assertEquals($value, $validationResult->getValue());
        $this->assertEmpty($validationResult->getValidationProblems());
    }
}
