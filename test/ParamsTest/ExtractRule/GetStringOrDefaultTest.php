<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\SingleValueDataLocator;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetStringOrDefault;
use Params\ParamsValuesImpl;
use Params\Path;
use Params\DataLocator\NotAvailableDataLocator;

/**
 * @coversNothing
 */
class GetStringOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [new ArrayVarMap(['foo' => 'bar']), 'john', 'bar'],
            [new ArrayVarMap([]), 'john', 'john'],


//            [new ArrayVarMap([]), null, null],
        ];
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidation()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap([]),
            $validator,
            SingleValueDataLocator::create('John')
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), 'John');
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidationForMissing()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ParamsValuesImpl();
        $validationResult = $rule->process(
            Path::fromName('foo'),
            new ArrayVarMap([]),
            $validator,
            new NotAvailableDataLocator()
        );

        $this->assertEmpty($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $default);
    }
}
