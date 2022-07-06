<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\Trim;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class TrimTest extends BaseTestCase
{
    /**
     * @covers \Type\ProcessRule\Trim
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
     * @covers \Type\ProcessRule\Trim
     */
    public function testDescription()
    {
        $rule = new Trim();
        $description = $this->applyRuleToDescription($rule);
        // nothing to test.
    }
}
