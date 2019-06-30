<?php

declare(strict_types=1);

namespace ParamsTest\Exception\Validator;

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
        $this->assertEmpty($validationResult->getProblemMessages());
    }

    public function testErrorResult()
    {
        $validationMessage = 'Something went wrong';
        $validationResult = ValidationResult::errorResult($validationMessage);

        $this->assertTrue($validationResult->isFinalResult());
        $this->assertNull($validationResult->getValue());
        $this->assertEquals($validationMessage, $validationResult->getProblemMessages()[0]);
    }

    public function testFinalValueResult()
    {
        $value = 5;
        $validationResult = ValidationResult::finalValueResult($value);
        $this->assertTrue($validationResult->isFinalResult());
        $this->assertEquals($value, $validationResult->getValue());
        $this->assertEmpty($validationResult->getProblemMessages());
    }
}
