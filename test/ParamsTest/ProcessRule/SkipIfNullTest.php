<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\SkipIfNull;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class SkipIfNullTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [null, true],
            [1, false],
            [0, false],
            [[], false],
            ['banana', false],

        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\SkipIfNull
     */
    public function testValidation($testValue, $expectIsFinalResult)
    {
        $rule = new SkipIfNull();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator
        );
        $this->assertEquals($validationResult->isFinalResult(), $expectIsFinalResult);
    }
}
