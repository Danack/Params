<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use Params\DataLocator\SingleValueInputStorageAye;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalString;
use Params\ProcessedValuesImpl;
use Params\Path;
use Params\DataLocator\NotAvailableInputStorageAye;

/**
 * @coversNothing
 */
class GetOptionalStringTest extends BaseTestCase
{
    /**
     * @covers \Params\ExtractRule\GetOptionalString
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalString();
        $validator = new ProcessedValuesImpl();

        $validationResult = $rule->process(
            $validator,
            new NotAvailableInputStorageAye()
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalString
     */
    public function testValidation()
    {

        $expectedValue = 'bar';

        $varMap = new ArrayVarMap([]);
        $rule = new GetOptionalString();
        $validator = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }
}
