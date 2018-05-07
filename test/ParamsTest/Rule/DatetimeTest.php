<?php

declare(strict_types=1);

namespace ParamsTest\Api\Params\Validator;

use ParamsTest\BaseTestCase;
use Params\Rule\ValidDatetime;

class DatetimeValidatorTest extends BaseTestCase
{

    public function provideTestCases()
    {
        return [
            ['Mon, 15 Aug 05 15:52:01', false, \Datetime::createFromFormat('Y-m-d H:i:s', '2005-08-15 15:52:01')],
            ['Banana', true, null],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\Rule\ValidDatetime
     */
    public function testValidation($datetime, $expectError, $expectedTime)
    {
        $validator = new ValidDatetime();
        $validationResult = $validator('foo', $datetime);

        if ($expectError) {
            $this->assertNotNull($validationResult->getProblemMessage());
            return;
        }

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedTime);
    }
}
