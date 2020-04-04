<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\SingleValueDataLocator;
use Params\DataLocator\StandardDataLocator;
use Params\ProcessRule\FloatInput;
use ParamsTest\BaseTestCase;
use Params\ParamsValuesImpl;
use Params\Path;
use function Params\createPath;

/**
 * @coversNothing
 */
class FloatInputTest extends BaseTestCase
{
    public function provideWorksCases()
    {
        return [
            ['5', 5],
            ['555555', 555555],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
        ];
    }

    /**
     * @dataProvider provideWorksCases
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testValidationWorks(string $inputValue, float $expectedValue)
    {
        $rule = new FloatInput();
        $validator = new ParamsValuesImpl();
        $dataLocator = StandardDataLocator::fromArray([]);
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $inputValue,
            $validator,
            $dataLocator
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideErrorCases()
    {
        return [
            // todo - we should test the exact error.
            ['5.a'],
            ['5.5 '], // trailing space
            [' 5.5'], // leading space
            ['5.5banana'], // trailing invalid chars
            ['banana'],
        ];
    }

    /**
     * @dataProvider provideErrorCases
     * @covers \Params\ProcessRule\FloatInput
     */
    public function testValidationErrors(string $inputValue)
    {
        $rule = new FloatInput();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $inputValue,
            $validator,
            SingleValueDataLocator::create($inputValue)
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
