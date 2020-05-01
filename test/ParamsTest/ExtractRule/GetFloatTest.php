<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetFloat;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class GetFloatTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetFloat
     */
    public function testMissingGivesError()
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            DataStorage::createMissing('foo')
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
     * @covers \Params\ExtractRule\GetFloat
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {
        $variableName = 'foo';
        $validator = new ProcessedValues();
        $rule = new GetFloat();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue([$variableName => $input])
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
     * @covers \Params\ExtractRule\GetFloat
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($value)
    {
        $rule = new GetFloat();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            DataStorage::fromSingleValue('foo', $value)
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::FLOAT_REQUIRED,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Params\ExtractRule\GetFloat
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
