<?php

declare(strict_types = 1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetArrayOfInt;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpec\DataStorage\TestArrayDataStorage;

/**
 * @coversNothing
 */
class GetArrayOfIntTest extends BaseTestCase
{
    /**
     * @covers  \TypeSpec\ExtractRule\GetArrayOfInt
     */
    public function testWorks()
    {
        $data = [5, 6, 7];

        $input = ['foo' => $data];

        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue($input);

        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($result);
        $this->assertFalse($result->isFinalResult());
        $this->assertSame($data, $result->getValue());
    }


    /**
     * @covers \TypeSpec\ExtractRule\GetArrayOfInt
     */
    public function testMissingGivesError()
    {
        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_SET]
        );
    }


    /**
     * @covers \TypeSpec\ExtractRule\GetArrayOfInt
     */
    public function testNotAnArrayErrors()
    {
        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();

        $input = 'banana';

        $validationResult = $rule->process(
            $validator,
            $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input)
        );

        $this->assertProblems(
            $validationResult,
            ['/foo' => Messages::ERROR_MESSAGE_NOT_ARRAY]
        );
    }






    /**
     * @covers  \TypeSpec\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnType()
    {
        $data = [5, 6, 7, 'banana'];


        $rule = new GetArrayOfInt();
        $validator = new ProcessedValues();
        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $validationProblems = $result->getValidationProblems();

        $this->assertCount(1, $validationProblems);
        $this->assertValidationProblem(
            '/[3]',
            'Value must contain only digits.',
            $validationProblems
        );
    }

    /**
     * @covers  \TypeSpec\ExtractRule\GetArrayOfInt
     */
    public function testErrorsOnSubsequentRule()
    {
        $data = [5, 6, 7, 5001];

        $rule = new GetArrayOfInt(
            new MaxIntValue(20)
        );
        $validator = new ProcessedValues();

        $result = $rule->process(
            $validator, TestArrayDataStorage::fromArray($data)
        );

        $this->assertTrue($result->isFinalResult());

        $problemMessages = $result->getValidationProblems();

        $this->assertValidationProblem(
            '/[3]',
            'Value too large. Max allowed is 20',
            $problemMessages
        );
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetArrayOfInt
     */
    public function testDescription()
    {
        $rule = new GetArrayOfInt();
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }
}
