<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\ProcessRule\MaximumCount;
use ParamsTest\BaseTestCase;
use Params\ProcessRule\NotNull;
use Params\ProcessedValues;

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
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule1->process(
            null, $processedValues, $dataLocator
        );
        $this->assertExpectedValidationProblems($validationResult->getValidationProblems());

        $rule2 = new NotNull();
        $processedValues = new ProcessedValues();
        $dataLocator = DataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule2->process(
            5, $processedValues, $dataLocator
        );
        $this->assertNoValidationProblems($validationResult->getValidationProblems());
    }


    /**
     * @covers \Params\ProcessRule\NotNull
     */
    public function testDescription()
    {
        $rule = new NotNull();
        $description = $this->applyRuleToDescription($rule);

        // Not null -> null will fail.
        $this->assertFalse($description->getNullAllowed());
    }
}
