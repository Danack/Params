<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use Type\OpenApi\OpenApiV300ParamDescription;
use Type\ProcessRule\CastToBool;
use ParamsTest\BaseTestCase;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class BoolInputTest extends BaseTestCase
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
     * @covers \Type\ProcessRule\CastToBool
     */
    public function testValidationWorks($inputValue, bool $expectedValue)
    {
        $rule = new CastToBool();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromArraySetFirstValue([$inputValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($expectedValue, $validationResult->getValue());
    }

    public function provideBoolValueErrorsCases()
    {
        yield [fopen('php://memory', 'r+'), Messages::UNSUPPORTED_TYPE];
        yield [[1, 2, 3], Messages::UNSUPPORTED_TYPE];
        yield [new \StdClass(), Messages::UNSUPPORTED_TYPE];
    }

    /**
     * @dataProvider provideBoolValueErrorsCases
     * @covers \Type\ProcessRule\CastToBool
     */
    public function testValidationErrors($inputValue, $message)
    {
        $rule = new CastToBool();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $inputValue,
            $processedValues,
            TestArrayDataStorage::fromSingleValue('foo', [$inputValue])
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $message,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Type\ProcessRule\CastToBool
     */
    public function testDescription()
    {
        $rule = new CastToBool();
        $description = $this->applyRuleToDescription($rule);
        $this->assertSame('boolean', $description->getType());
    }
}
