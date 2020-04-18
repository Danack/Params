<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\NotNull;
use Params\ProcessedValuesImpl;

/**
 * @coversNothing
 */
class NotNullTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\NotNull
     */
    public function testValidation()
    {
        $rule1 = new NotNull();
        $processedValues = new ProcessedValuesImpl();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule1->process(
            null, $processedValues, $dataLocator
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());

        $rule2 = new NotNull();
        $processedValues = new ProcessedValuesImpl();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule2->process(
            5, $processedValues, $dataLocator
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
    }
}
