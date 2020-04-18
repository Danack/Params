<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
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
            ' bar ', $processedValues, DataStorage::fromArraySetFirstValue([' bar '])
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), 'bar');
    }
}
