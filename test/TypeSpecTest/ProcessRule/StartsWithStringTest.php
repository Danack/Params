<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpec\ProcessRule\MultipleEnum;
use TypeSpec\ProcessRule\CheckOnlyAllowedCharacters;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\StartsWithString;
use TypeSpec\ProcessedValues;

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
     * @covers \TypeSpec\ProcessRule\StartsWithString
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
     * @covers \TypeSpec\ProcessRule\StartsWithString
     */
    public function testValidationErrors(string $prefix, $testValue)
    {
        $rule = new StartsWithString($prefix);
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $testValue);
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
     * @covers \TypeSpec\ProcessRule\StartsWithString
     */
    public function testDescription()
    {
        $prefix = 'bar_';

        $rule = new StartsWithString($prefix);
        $description = $this->applyRuleToDescription($rule);
    }
}
