<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\DataLocator\SingleValueInputStorageAye;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessedValuesImpl;
use Params\Path;
use Params\DataLocator\NotAvailableInputStorageAye;

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
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue(['John'])
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), 'John');
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidationForMissing()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator, new NotAvailableInputStorageAye()
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $default);
    }
}
