<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\ValidDate;
use Params\ParamsValuesImpl;
use Params\Path;

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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $input,
            $validator
        );

        $this->assertEmpty($validationResult->getValidationProblems());
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
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $input,
            $validator
        );

        $this->assertNotNull($validationResult->getValidationProblems());
    }
}
