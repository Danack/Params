<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\StandardDataLocator;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetInt;
use Params\ParamsValuesImpl;
use Params\Path;
use Params\DataLocator\SingleValueDataLocator;
use Params\DataLocator\NotAvailableDataLocator;

/**
 * @coversNothing
 */
class GetIntTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetInt();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap([]),
            $validator,
            new NotAvailableDataLocator()
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
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
        $variableName = 'foo';
        $validator = new ParamsValuesImpl();
        $rule = new GetInt();
        $dataLocator  = SingleValueDataLocator::create($input);

        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap([$variableName => $input]),
            $validator,
            $dataLocator
        );

        $this->assertEmpty($validationResult->getValidationProblems());
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
        $variableName = 'foo';

        $rule = new GetInt();
        $validator = new ParamsValuesImpl();
        $dataLocator  = SingleValueDataLocator::create($input);

        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap([]),
            $validator,
            $dataLocator
        );

        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }
}
