<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\DataLocator\DataStorage;
use Params\Messages;
use Params\OpenApi\OpenApiV300ParamDescription;
use Params\ProcessRule\DuplicatesParam;
use ParamsTest\BaseTestCase;
use Params\ProcessedValues;

/**
 * @coversNothing
 */
class DuplicatesParamTest extends BaseTestCase
{
    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testWorks()
    {
        $value = 'my_voice_is_my_password';
        $processedValues = ProcessedValues::fromArray(['foo' => $value]);
        $dataLocator = DataStorage::fromArray([]);

        $rule = new DuplicatesParam('foo');
        $validationResult = $rule->process($value, $processedValues, $dataLocator);

        $this->assertNoValidationProblems($validationResult->getValidationProblems());

        $this->assertSame($value, $validationResult->getValue());
        $this->assertFalse($validationResult->isFinalResult());
    }

    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testMissing()
    {
        $value = 'my_voice_is_my_password';
        $processedValues = ProcessedValues::fromArray([]);
        $dataLocator = DataStorage::fromArray([]);
        $dataLocator = $dataLocator->moveKey('foo');

        $rule = new DuplicatesParam('foo');
        $validationResult = $rule->process($value, $processedValues, $dataLocator);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_NO_PREVIOUS_PARAM,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }

    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testWrongType()
    {
        $processedValues = ProcessedValues::fromArray(['foo' => 'my_voice_is_my_password']);
        $dataLocator = DataStorage::fromArray([]);

        $rule = new DuplicatesParam('foo');
        $dataLocator = $dataLocator->moveKey('foo');
        $validationResult = $rule->process(12345, $processedValues, $dataLocator);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_DIFFERENT_TYPES,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }


    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testWrongValue()
    {
        $processedValues = ProcessedValues::fromArray(['foo' => 'my_voice_is_my_password']);
        $dataLocator = DataStorage::fromArray([]);

        $rule = new DuplicatesParam('foo');
        $dataLocator = $dataLocator->moveKey('foo');
        $validationResult = $rule->process('not_the_same', $processedValues, $dataLocator);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::ERROR_DIFFERENT_VALUE,
            $validationResult->getValidationProblems()
        );

        $this->assertCount(1, $validationResult->getValidationProblems());
        $this->assertTrue($validationResult->isFinalResult());
    }



    /**
     * @covers \Params\ProcessRule\DuplicatesParam
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');

        $parameterName = 'foo';

        $rule = new DuplicatesParam($parameterName);
        $rule->updateParamDescription($description);

        $this->assertStringRegExp(
            Messages::MUST_DUPLICATE_PARAMETER,
            $description->getDescription()
        );

        $this->assertStringContainsString($parameterName, $description->getDescription());
    }
}
