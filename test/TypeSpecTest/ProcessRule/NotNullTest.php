<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\NotNull;
use TypeSpec\ProcessedValues;

/**
 * @coversNothing
 */
class NotNullTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessRule\NotNull
     */
    public function testValidation()
    {
        $rule1 = new NotNull();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', null);
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
     * @covers \TypeSpec\ProcessRule\NotNull
     */
    public function testDescription()
    {
        $rule = new NotNull();
        $description = $this->applyRuleToDescription($rule);

        // Not null -> null will fail.
        $this->assertFalse($description->getNullAllowed());
    }
}
