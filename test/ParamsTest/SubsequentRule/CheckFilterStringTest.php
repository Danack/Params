<?php

declare(strict_types=1);

namespace ParamsTest\Rule;

use Params\Value\MultipleEnums;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\MultipleEnum;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class CheckFilterStringTest extends BaseTestCase
{
    public function providesKnownFilterCorrect()
    {
        return [
            ['foo', ['foo']],
            ['bar,foo', ['bar', 'foo']],
        ];
    }

    /**
     * @dataProvider providesKnownFilterCorrect
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testKnownFilterCorrect($inputString, $expectedResult)
    {
        $rule = new MultipleEnum(['foo', 'bar']);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('someFilter'),
            $inputString,
            $validator
        );
        $this->assertEmpty($validationResult->getValidationProblems());

        $validationValue = $validationResult->getValue();

        $this->assertInstanceOf(MultipleEnums::class, $validationValue);
        /** @var $validationValue \Params\Value\MultipleEnums */

        $this->assertEquals($expectedResult, $validationValue->getValues());
    }

    /**
     * @covers \Params\ProcessRule\MultipleEnum
     */
    public function testUnknownFilterErrors()
    {
        $expectedValue = 'zot';
        $rule = new MultipleEnum(['foo', 'bar']);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('someFilter'),
            $expectedValue,
            $validator
        );
        $this->assertNotNull($validationResult->getValidationProblems());
    }
}
