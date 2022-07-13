<?php

declare(strict_types=1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use VarMap\ArrayVarMap;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetStringOrDefault;
use TypeSpec\ProcessedValues;

/**
 * @coversNothing
 */
class GetStringOrDefaultTest extends BaseTestCase
{
    public function provideTestCases()
    {
        return [
            [new ArrayVarMap(['foo' => 'bar']), 'john', 'bar'],
            [new ArrayVarMap([]), 'john', 'john'],


//            [new ArrayVarMap([]), null, null],
        ];
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetStringOrDefault
     */
    public function testValidation()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, TestArrayDataStorage::fromArraySetFirstValue(['John'])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), 'John');
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetStringOrDefault
     */
    public function testValidationForMissing()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            TestArrayDataStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $default);
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetStringOrDefault
     */
    public function testGetDefault()
    {
        $default = 'bar';
        $rule = new GetStringOrDefault($default);
        $this->assertSame($default, $rule->getDefault());
    }


    /**
     * @covers \TypeSpec\ExtractRule\GetStringOrDefault
     */
    public function testDescription()
    {
        $rule = new GetStringOrDefault('John');
        $description = $this->applyRuleToDescription($rule);

        $rule->updateParamDescription($description);
        $this->assertSame('string', $description->getType());
        $this->assertFalse($description->getRequired());
        $this->assertSame('John', $description->getDefault());
    }
}
