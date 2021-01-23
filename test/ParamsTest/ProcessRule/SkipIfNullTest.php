<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\SkipIfNull;
use Params\ProcessedValues;

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
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );
        $this->assertEquals($validationResult->isFinalResult(), $expectIsFinalResult);
    }
}
