<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\OpenApi\OpenApiV300ParamDescription;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\Trim;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class TrimTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\Trim
     */
    public function testValidation()
    {
        $rule = new Trim();
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            ' bar ', $processedValues, ArrayInputStorage::fromArraySetFirstValue([' bar '])
        );
        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), 'bar');
    }


    /**
     * @covers \Params\ProcessRule\Trim
     */
    public function testDescription()
    {
        $rule = new Trim();
        $description = $this->applyRuleToDescription($rule);
        // nothing to test.
    }
}
