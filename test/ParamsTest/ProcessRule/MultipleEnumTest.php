<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use ParamsTest\BaseTestCase;
use Params\ProcessRule\MultipleEnum;
use Params\Value\MultipleEnums;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class MultipleEnumTest extends BaseTestCase
{
    public function provideMultipleEnumCases()
    {
        return [
            ['foo,', ['foo']],
            [',,,,,foo,', ['foo']],
        ];
    }

    /**
     * @dataProvider provideMultipleEnumCases
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testMultipleEnum_emptySegments($input, $expectedOutput)
    {
        $enumRule = new MultipleEnum(['foo', 'bar']);
        $validator = new ParamsValuesImpl();
        $result = $enumRule->process(
            Path::fromName('unused'),
            $input,
            $validator
        );

        $this->assertEmpty($result->getValidationProblems());
        $value = $result->getValue();
        $this->assertInstanceOf(MultipleEnums::class, $value);
        $this->assertEquals($expectedOutput, $value->getValues());
    }

    // TODO - these appear to be duplicate tests.
    public function provideTestCases()
    {
        return [
            ['time', ['time'], false],
            ['bar', null, true],
        ];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testValidation($testValue, $expectedFilters, $expectError)
    {
        $rule = new MultipleEnum(['time', 'distance']);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            $testValue,
            $validator
        );

        if ($expectError === true) {
            $this->assertNotNull($validationResult->getValidationProblems());
            return;
        }

        $value = $validationResult->getValue();
        $this->assertInstanceOf(\Params\Value\MultipleEnums::class, $value);

        /** @var $value \Params\Value\MultipleEnums */
        $this->assertEquals($expectedFilters, $value->getValues());
    }
}
