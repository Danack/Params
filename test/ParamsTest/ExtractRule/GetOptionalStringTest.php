<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Type\DataStorage\TestArrayDataStorage;
use ParamsTest\BaseTestCase;
use Type\ExtractRule\GetOptionalString;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class GetOptionalStringTest extends BaseTestCase
{
    /**
     * @covers \Type\ExtractRule\GetOptionalString
     */
    public function testMissingGivesNull()
    {
        $rule = new GetOptionalString();
        $validator = new ProcessedValues();

        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );
        $this->assertNoProblems($validationResult);
        $this->assertNull($validationResult->getValue());
    }

    /**
     * @covers \Type\ExtractRule\GetOptionalString
     */
    public function testValidation()
    {

        $expectedValue = 'bar';

        $rule = new GetOptionalString();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::fromArraySetFirstValue([$expectedValue])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expectedValue);
    }


    /**
     * @covers \Type\ExtractRule\GetOptionalString
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
