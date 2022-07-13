<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\Trim;
use TypeSpec\ProcessedValues;

/**
 * @coversNothing
 */
class TrimTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessRule\Trim
     */
    public function testValidation()
    {
        $rule = new Trim();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            ' bar ', $processedValues, TestArrayDataStorage::fromArraySetFirstValue([' bar '])
        );
        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), 'bar');
    }


    /**
     * @covers \TypeSpec\ProcessRule\Trim
     */
    public function testDescription()
    {
        $rule = new Trim();
        $description = $this->applyRuleToDescription($rule);
        // nothing to test.
    }
}
