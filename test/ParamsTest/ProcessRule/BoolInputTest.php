<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\SingleValueInputStorageAye;
use Params\DataLocator\DataStorage;
use Params\ProcessRule\BoolInput;
use ParamsTest\BaseTestCase;
use Params\ProcessedValuesImpl;
use Params\Path;
use function Params\createPath;

/**
 * @coversNothing
 */
class BoolInputValidatorTest extends BaseTestCase
{
    public function provideBoolValueWorksCases()
    {
        return [
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
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testValidationWorks($inputValue, bool $expectedValue)
    {
        $rule = new BoolInput();
        $processedValues = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            SingleValueInputStorageAye::create($inputValue)
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
     * @covers \Params\ProcessRule\IntegerInput
     */
    public function testValidationErrors($inputValue)
    {
        $rule = new BoolInput();
        $processedValues = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            SingleValueInputStorageAye::create($inputValue)
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
