<?php

declare(strict_types=1);

namespace ParamsTest\Api\Params\Validator;

use ParamsTest\BaseTestCase;
use Params\Rule\ValidDatetime;

class DatetimeValidatorTest extends BaseTestCase
{

    public function provideTestWorksCases()
    {
        return [
            [
                '2002-10-02T10:00:00-05:00',
                \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T10:00:00-05:00')
            ],
            [
                '2002-10-02T15:00:00Z',
                \DateTime::createFromFormat(\DateTime::RFC3339, '2002-10-02T15:00:00Z')
            ],

            // This should work - but currently doesn't
//            [
//                '2002-10-02T15:00:00.05Z',
//                \DateTime::createFromFormat(\DateTime::RFC3339_EXTENDED, '2002-10-02T15:00:00.05Z')
//            ],
        ];
    }




    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\Rule\ValidDatetime
     */
    public function testValidationWorks($input, $expectedTime)
    {
        $validator = new ValidDatetime();
        $validationResult = $validator('foo', $input);

        $this->assertNull($validationResult->getProblemMessage());
        $this->assertEquals($validationResult->getValue(), $expectedTime);
    }

    public function provideTestErrorsCases()
    {
        return [
            ['2pm on Tuesday'],
            ['Banana'],
        ];
    }

    /**
     * @dataProvider provideTestErrorsCases
     * @covers \Params\Rule\ValidDatetime
     */
    public function testValidationErrors($input)
    {
        $validator = new ValidDatetime();
        $validationResult = $validator('foo', $input);

        $this->assertNotNull($validationResult->getProblemMessage());
    }
}
