<?php

declare(strict_types=1);

namespace ParamsTest\ExtractRule;

use Params\InputStorage\ArrayInputStorage;
use VarMap\ArrayVarMap;
use ParamsTest\BaseTestCase;
use Params\ExtractRule\GetStringOrDefault;
use Params\ProcessedValues;

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
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidation()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator, ArrayInputStorage::fromArraySetFirstValue(['John'])
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), 'John');
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
     */
    public function testValidationForMissing()
    {
        $default = 'bar';

        $rule = new GetStringOrDefault($default);
        $validator = new ProcessedValues();
        $validationResult = $rule->process(
            $validator,
            ArrayInputStorage::createMissing('foo')
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $default);
    }

    /**
     * @covers \Params\ExtractRule\GetStringOrDefault
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
