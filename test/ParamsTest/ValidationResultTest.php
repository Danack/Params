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
        $name = 'foo';
        $key = '/' . $name;
        $validationMessage = 'Something went wrong';
        $validationResult = ValidationResult::errorResult($name, $validationMessage);

        $this->assertTrue($validationResult->isFinalResult());
        $this->assertNull($validationResult->getValue());

        $messages = $validationResult->getProblemMessages();
        $this->assertArrayHasKey($key, $messages);
        $this->assertEquals($validationMessage, $messages[$key]);
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
