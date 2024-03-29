<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpec\ProcessRule\DuplicatesParam;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessedValues;

/**
 * @coversNothing
 */
class DuplicatesParamTest extends BaseTestCase
{
    /**
     * @covers \TypeSpec\ProcessRule\DuplicatesParam
     */
    public function testWorks()
    {
        $value = 'my_voice_is_my_password';
//        $processedValues = ProcessedValues::fromArray(['foo' => $value]);
        $processedValues = createProcessedValuesFromArray(['foo' => $value]);
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $rule = new DuplicatesParam('foo');
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertNoProblems($validationResult);

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    /**
     * @covers \TypeSpec\ProcessRule\DuplicatesParam
     */
    public function testMissing()
    {
        $value = 'my_voice_is_my_password';
        $processedValues = createProcessedValuesFromArray([]);
        $dataStorage = TestArrayDataStorage::fromArray([]);
        $dataStorage = $dataStorage->moveKey('foo');

        $rule = new DuplicatesParam('foo');
        $validationResult = $rule->process($value, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_NO_PREVIOUS_PARAMETER,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \TypeSpec\ProcessRule\DuplicatesParam
     */
    public function testWrongType()
    {
//        $processedValues = ProcessedValues::fromArray(['foo' => 'my_voice_is_my_password']);
        $processedValues = createProcessedValuesFromArray(['foo' => 'my_voice_is_my_password']);
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $rule = new DuplicatesParam('foo');
        $dataStorage = $dataStorage->moveKey('foo');
        $validationResult = $rule->process(12345, $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_DIFFERENT_TYPES,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \TypeSpec\ProcessRule\DuplicatesParam
     */
    public function testWrongValue()
    {
//        $processedValues = ProcessedValues::fromArray(['foo' => 'my_voice_is_my_password']);
        $processedValues = createProcessedValuesFromArray(['foo' => 'my_voice_is_my_password']);
        $dataStorage = TestArrayDataStorage::fromArray([]);

        $rule = new DuplicatesParam('foo');
        $dataStorage = $dataStorage->moveKey('foo');
        $validationResult = $rule->process('not_the_same', $processedValues, $dataStorage);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_DIFFERENT_VALUE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }



    /**
     * @covers \TypeSpec\ProcessRule\DuplicatesParam
     */
    public function testDescription()
    {
        $parameterName = 'foo';

        $rule = new DuplicatesParam($parameterName);
        $description = $this->applyRuleToDescription($rule);

        $this->assertStringMatchesTemplateString(
            Messages::MUST_DUPLICATE_PARAMETER,
            $description->getDescription()
        );

        $this->assertStringContainsString($parameterName, $description->getDescription());
    }
}
