<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\BoolInput;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class BoolInputValidatorTest extends BaseTestCase
{
    public function provideBoolValueWorksCases()
    {
        return [

            [true, true],
            [false, false],
            [null, false],
            ['true', true],
            ['truuue', false],

            [0, false],
            [1, true],
            [2, true],
            [-5000, true],
        ];
    }

    /**
     * @dataProvider provideBoolValueWorksCases
     * @covers \Params\ProcessRule\BoolInput
     */
    public function testValidationWorks($inputValue, bool $expectedValue)
    {
        $rule = new BoolInput();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            DataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideBoolValueErrorsCases()
    {
        return [
            // todo - we should test the exact error.
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \StdClass()]
        ];
    }

    /**
     * @dataProvider provideBoolValueErrorsCases
     * @covers \Params\ProcessRule\BoolInput
     */
    public function testValidationErrors($inputValue)
    {
        $rule = new BoolInput();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            DataStorage::fromArraySetFirstValue([$inputValue])
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }

    /**
     * @covers \Params\ProcessRule\BoolInput
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');
        $rule = new BoolInput();
        $rule->updateParamDescription($description);
        $this->assertSame('boolean', $description->getType());
    }
}
