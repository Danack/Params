<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\ExtractRule\GetBool;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class GetBoolTest extends BaseTestCase
{
    /**
     * @covers \Type\ExtractRule\GetBool
     */
    public function testMissingGivesError()
    {
        $rule = new GetBool();
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
        yield ['true', true];
        yield ['truuue', false];
        yield [null, false];

        yield [0, false];
        yield [1, true];
        yield [2, true];
        yield [-5000, true];
    }

    /**
     * @covers \Type\ExtractRule\GetBool
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {

        $validator = new ProcessedValues();
        $rule = new GetBool();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromSingleValue('foo', $input)
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }

    public function provideTestErrorCases()
    {
        return [
            // todo - we should test the exact error.
            [fopen('php://memory', 'r+')],
            [[1, 2, 3]],
            [new \StdClass()]
        ];
    }

    /**
     * @covers \Type\ExtractRule\GetBool
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($value)
    {
        $rule = new GetBool();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue(['foo' => $value])
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::UNSUPPORTED_TYPE,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Type\ExtractRule\GetBool
     */
    public function testDescription()
    {
        $rule = new GetBool();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('boolean', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
