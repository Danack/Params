<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetOptionalString;
use Params\ProcessedValues;
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
        $validator = new ProcessedValues();

        $validationResult = $rule->process(
            $validator,
            new NotAvailableInputStorageAye()
        );
        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Params\ExtractRule\GetOptionalString
     */
    public function testValidation()
    {

        $expectedValue = 'bar';

        $rule = new GetOptionalString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, DataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    /**
     * @covers \Params\ExtractRule\GetOptionalString
     */
    public function testDescription()
    {
        $rule = new GetOptionalString();
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertFalse($description->getRequired());
    }
}
