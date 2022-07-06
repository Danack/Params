<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\NotNull;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class NotNullTest extends BaseTestCase
{
    /**
     * @covers \Type\ProcessRule\NotNull
     */
    public function testValidation()
    {
        $rule1 = new NotNull();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', null);
        $validationResult = $rule1->process(
            null,
            $processedValues,
            $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::NULL_NOT_ALLOWED,
            $validationResult->getValidationProblems()
        );

        $rule2 = new NotNull();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $validationResult = $rule2->process(
            5, $processedValues, $dataStorage
        );
        $this->assertNoProblems($validationResult);
    }


    /**
     * @covers \Type\ProcessRule\NotNull
     */
    public function testDescription()
    {
        $rule = new NotNull();
        $description = $this->applyRuleToDescription($rule);

        // Not null -> null will fail.
        $this->assertFalse($description->getNullAllowed());
    }
}
