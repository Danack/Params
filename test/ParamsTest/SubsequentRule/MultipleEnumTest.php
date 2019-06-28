<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use ParamsTest\BaseTestCase;
use Params\SubsequentRule\MultipleEnum;
use Params\Value\MultipleEnums;
use Params\ParamsValidator;

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
     * @covers \Params\SubsequentRule\MultipleEnum
     */
    public function testMultipleEnum_emptySegments($input, $expectedOutput)
    {
        $enumRule = new MultipleEnum(['foo', 'bar']);
        $validator = new ParamsValidator();
        $result = $enumRule->process('unused', $input, $validator);

        $this->assertNull($result->getProblemMessage());
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
     * @covers \Params\SubsequentRule\MultipleEnum
     */
    public function testValidation($testValue, $expectedFilters, $expectError)
    {
        $rule = new MultipleEnum(['time', 'distance']);
        $validator = new ParamsValidator();
        $validationResult = $rule->process('foo', $testValue, $validator);

        if ($expectError === true) {
            $this->assertNotNull($validationResult->getProblemMessage());
            return;
        }

        $value = $validationResult->getValue();
        $this->assertInstanceOf(\Params\Value\MultipleEnums::class, $value);

        /** @var $value \Params\Value\MultipleEnums */
        $this->assertEquals($expectedFilters, $value->getValues());
    }
}
