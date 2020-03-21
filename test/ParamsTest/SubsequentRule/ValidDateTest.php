<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\ValidDate;
use Params\ParamsValidator;

/**
 * @coversNothing
 */
class ValidDateTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
            [
                '2002-10-02',
                \DateTime::createFromFormat('Y-m-d', '2002-10-02')->setTime(0, 0, 0, 0)
            ],
        ];
    }


    /**
     * @dataProvider provideTestWorksCases
     * @covers \Params\ProcessRule\ValidDate
     */
    public function testValidationWorks($input, $expectedTime)
    {
        $rule = new ValidDate();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $input, $validator);

        $this->assertEmpty($validationResult->getProblemMessages());
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
     * @covers \Params\ProcessRule\ValidDate
     */
    public function testValidationErrors($input)
    {
        $rule = new ValidDate();
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $input, $validator);

        $this->assertNotNull($validationResult->getProblemMessages());
    }
}
