<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetInt;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class GetIntTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetInt
     */
    public function testMissingGivesError()
    {
        $rule = new GetInt();
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
            [5, 5],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetInt
     * @dataProvider provideTestWorksCases
     */
    public function testWorks($input, $expectedValue)
    {
        $validator = new ProcessedValues();
        $rule = new GetInt();
        $dataLocator  = DataStorage::fromSingleValue('foo', $input);

        $validationResult = $rule->process(
            $validator, $dataLocator
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    public function provideTestErrorCases()
    {
        yield [null];
        yield [''];
        yield ['6 apples'];
        yield ['banana'];
        // TODO add expected error string
    }

    /**
     * @covers \Params\ExtractRule\GetInt
     * @dataProvider provideTestErrorCases
     */
    public function testErrors($input)
    {
        $rule = new GetInt();
        $validator = new ProcessedValues();
        $dataLocator = DataStorage::fromSingleValue('foo', $input);

        $validationResult = $rule->process(
            $validator, $dataLocator
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }

    /**
     * @covers \Params\ExtractRule\GetInt
     */
    public function testDescription()
    {
        $rule = new GetInt();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('integer', $description->getType());
        $this->assertTrue($description->getRequired());
    }
}
