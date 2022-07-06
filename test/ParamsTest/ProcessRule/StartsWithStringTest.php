<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Type\DataStorage\TestArrayDataStorage;
use Type\Messages;
use Type\OpenApi\OpenApiV300ParamDescription;
use Type\ProcessRule\MultipleEnum;
use Type\ProcessRule\ValidCharacters;
use ParamsTest\BaseTestCase;
use Type\ProcessRule\StartsWithString;
use Type\ProcessedValues;

/**
 * @coversNothing
 */
class StartsWithStringTest extends BaseTestCase
{
    public function provideTestWorksCases()
    {
        return [
            ['pk_', 'pk_foobar'],
            ['_', '_foobar'],
        ];
    }

    /**
     * @dataProvider provideTestWorksCases
     * @covers \Type\ProcessRule\StartsWithString
     */
    public function testValidationWorks(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $dataStorage = TestArrayDataStorage::fromArraySetFirstValue([]);
        $processedValues = new ProcessedValues();
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertSame($validationResult->getValue(), $testValue);
    }

    public function provideTestFailsCases()
    {
        return [
            ['pk_', 'dk_foobar'],
            ['_', 'f_oobar', true],
        ];
    }

    /**
     * @dataProvider provideTestFailsCases
     * @covers \Type\ProcessRule\StartsWithString
     */
    public function testValidationErrors(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);
        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::STRING_REQUIRES_PREFIX,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \Type\ProcessRule\StartsWithString
     */
    public function testDescription()
    {
        $prefix = 'bar_';

        $rule = new StartsWithString($prefix);
        $description = $this->applyRuleToDescription($rule);
    }
}
