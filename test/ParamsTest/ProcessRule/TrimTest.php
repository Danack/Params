<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\SingleValueInputStorageAye;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\Trim;
use Params\ProcessedValuesImpl;
use Params\Path;
use function Params\createPath;

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
        $processedValues = new ProcessedValuesImpl();
        $validationResult = $rule->process(
            ' bar ', $processedValues, SingleValueInputStorageAye::create(' bar ')
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
        $this->assertEquals($validationResult->getValue(), 'bar');
    }
}
