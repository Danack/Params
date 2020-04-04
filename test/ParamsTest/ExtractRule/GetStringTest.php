<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\NotAvailableDataLocator;
use Params\DataLocator\SingleValueDataLocator;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetString;
use Params\ParamsValuesImpl;
use Params\Path;

/**
 * @coversNothing
 */
class GetStringTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testMissingGivesError()
    {
        $rule = new GetString();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap([]),
            $validator,
            new NotAvailableDataLocator()
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());
    }

    /**
     * @covers \Params\ExtractRule\GetString
     */
    public function testValidation()
    {
        $variableName = 'foo';
        $expectedValue = 'bar';

        $rule = new GetString();
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName($variableName),
            new ArrayVarMap([$variableName => $expectedValue]),
            $validator,
            SingleValueDataLocator::create('John')
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
