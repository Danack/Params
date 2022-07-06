<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\ExtractRule\GetFloat;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class GetFloatTest extends BaseTestCase
{
    /**
     * @covers \Type\ExtractRule\GetFloat
     */
    public function testMissingGivesError()
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::VALUE_NOT_SET]
        );
    }

    public function provideTestWorksCases()
    {
        return [
            ['5', 5],
            ['555555', 555555],
            ['1000.1', 1000.1],
            ['-1000.1', -1000.1],
        ];
    }

    /**
     * @covers \Type\ExtractRule\GetFloat
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {
        $variableName = 'foo';
        $validator = new ProcessedValues();
        $rule = new GetFloat();
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue([$variableName => $input])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        yield ['5.a'];
        yield ['banana'];
    }

    /**
     * @covers \Type\ExtractRule\GetFloat
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($value)
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValue('foo', $value)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::FLOAT_REQUIRED,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Type\ExtractRule\GetFloat
     */
    public function testDescription()
    {
        $rule = new GetFloat();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('number', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
