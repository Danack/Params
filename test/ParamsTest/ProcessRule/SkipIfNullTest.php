<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataStorage\TestArrayDataStorage;
use Params\OpenApi\OpenApiV300ParamDescription;
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
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );
        $this->assertEquals($validationResult->isFinalResult(), $expectIsFinalResult);
    }

    /**
     * @covers \Params\ProcessRule\SkipIfNull
     */
    public function testDescription()
    {
        $rule = new SkipIfNull();
        $description = $this->applyRuleToDescription($rule);
    }
}
