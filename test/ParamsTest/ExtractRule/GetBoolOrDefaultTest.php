<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataStorage\TestArrayDataStorage;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetBoolOrDefault;
use Params\ProcessedValues;
use Params\Messages;

/**
 * @coversNothing
 */
class GetBoolOrDefaultTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetBoolOrDefault
     */
    public function testMissingCorrect()
    {
        $defaults = [true, false];

        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorage = $dataStorage->moveKey('foo');

        foreach ($defaults as $default) {
            $rule = new GetBoolOrDefault($default);
            $validator = new ProcessedValues();
            $validationResult = $rule->process(
                $validator,
                $dataStorage
            );
            $this->assertNoProblems($validationResult);
            $this->assertSame($default, $validationResult->getValue());
        }
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
     * @covers \Params\ExtractRule\GetBoolOrDefault
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {

        $validator = new ProcessedValues();
        $rule = new GetBoolOrDefault(false);
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
     * @covers \Params\ExtractRule\GetBoolOrDefault
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($value)
    {
        $rule = new GetBoolOrDefault(false);
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
     * @covers \Params\ExtractRule\GetBoolOrDefault
     */
    public function testDescription()
    {
        $defaults = [true, false];
        foreach ($defaults as $default) {
            $rule = new GetBoolOrDefault($default);
            $description = $this->applyRuleToDescription($rule);

            $rule->updateParamDescription($description);
            $this->assertSame('boolean', $description->getType());
            $this->assertFalse($description->getRequired());

            $this->assertSame($default, $description->getDefault());
        }
    }
}
