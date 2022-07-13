<?php

declare(strict_types=1);

namespace TypeSpecTest;

use TypeSpec\ExtractRule\GetInt;
use TypeSpec\InputTypeSpec;
use TypeSpec\ProcessRule\MaxIntValue;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessedValues;
use TypeSpec\ProcessRule\AlwaysEndsRule;
use TypeSpec\DataStorage\TestArrayDataStorage;
use function TypeSpec\processInputParameter;

/**
 * @coversNothing
 */
class ParamsValidatorTest extends BaseTestCase
{

//    public function testMissingRuleThrows()
//    {
//        $this->markTestSkipped("does this provide useful info?");
////        $validator = new ParamsValidator();
////        $this->expectException(\Params\Exception\ParamsException::class);
////        $validator->validate('foobar', []);
//    }

//    public function testInvalidInputThrows()
//    {
//        $arrayVarMap = new ArrayVarMap([]);
//        $validator = new ParamsValuesImpl();
//
//        $value = $validator->validateRulesForParam(
//            'foo',
//            $arrayVarMap,
//            new GetInt()
//        );
//
//        $this->assertNull($value);
//        $errors = $validator->getValidationProblems();
//
//        $this->assertEquals(1, count($errors));
//        $this->assertStringMatchesFormat(GetInt::ERROR_MESSAGE, $errors['/foo']);
//    }

    /**
     * @covers \TypeSpec\ProcessedValues
     */
    public function testFinalResultStopsProcessing()
    {
        $finalValue = 123;

        $param = new InputTypeSpec(
            'foo',
            new GetInt(),
            // This rule will stop processing
            new AlwaysEndsRule($finalValue),
            // this rule would give an error if processing was not stopped.
            new MaxIntValue($finalValue - 5)
        );

        $processedValues = new ProcessedValues();

        $errors = processInputParameter(
            $param,
            $processedValues,
            TestArrayDataStorage::fromArray(['foo' => 5])
        );

        $this->assertNoValidationProblems($errors);

        $this->assertTrue($processedValues->hasValue('foo'));
        $value = $processedValues->getValue('foo');

        $this->assertEquals($finalValue, $value);
    }
}
