<?php

declare(strict_types=1);

namespace TypeSpecTest\ProcessRule;

use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\OpenApi\OpenApiV300ParamDescription;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ProcessRule\Order;
use TypeSpec\Value\Ordering;
use TypeSpec\ProcessedValues;
use TypeSpec\Messages;
use TypeSpec\ProcessRule\MatrixIsSquare;

/**
 * @coversNothing
 */
class MatrixIsSquareTest extends BaseTestCase
{
    public function provideTestCases()
    {
        $values3 = [
            [1, 2, 3],
            [1, 2, 3],
            [1, 2, 3],
        ];

        $values1 = [
            [1],
        ];

        yield [$values1];
        yield [$values3];
    }

    /**
     * @dataProvider provideTestCases
     * @covers \TypeSpec\ProcessRule\MatrixIsSquare
     */
    public function testValidation($testValue)
    {
        $rule = new MatrixIsSquare();
        $processedValues = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    public function provideTestErrors()
    {
        $values1x3 = [
            [1, 2, 3],
        ];

        $values2x3 = [
            [1, 2, 3],
            [1, 2, 3],
        ];

        $values3x2 = [
            [1, 2],
            [1, 2],
            [2, 3],
        ];

        yield [$values1x3, Messages::MATRIX_MUST_BE_SQUARE, 1, 3];
        yield [$values2x3, Messages::MATRIX_MUST_BE_SQUARE, 2, 3];
        yield [$values3x2, Messages::MATRIX_MUST_BE_SQUARE, 3, 2];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \TypeSpec\ProcessRule\MatrixIsSquare
     */
    public function testErrors($testValue, $expectedErrorMessage, $rows, $columns)
    {
        $rule = new MatrixIsSquare();
        $processedValues = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataStorage
        );

        $sizeString = $rows . " x " . $columns;

        $this->assertOneErrorAndContainsString($validationResult, $sizeString);

        $this->assertValidationProblemRegexp(
            '/foo',
            Messages::MATRIX_MUST_BE_SQUARE,
            $validationResult->getValidationProblems()
        );
    }

    /**
     * @covers \TypeSpec\ProcessRule\MatrixIsSquare
     */
    public function testDescription()
    {
        $rule = new MatrixIsSquare();
        $description = $this->applyRuleToDescription($rule);
    }
}
