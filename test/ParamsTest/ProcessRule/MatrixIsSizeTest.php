<?php

declare(strict_types=1);

namespace ParamsTest\ProcessRule;

use Params\InputStorage\ArrayInputStorage;
use Params\Messages;
use Params\ProcessedValues;
use Params\ProcessRule\MatrixIsSize;
use ParamsTest\BaseTestCase;
use Params\OpenApi\OpenApiV300ParamDescription;

/**
 * @coversNothing
 */
class MatrixIsSizeTest extends BaseTestCase
{
    private $values3 = [
        [1, 2, 3],
        [1, 2, 3],
        [1, 2, 3],
    ];

    private $values1 = [
        [1],
    ];

    private $values1x3 = [
        [1, 2, 3],
    ];

    public function provideTestWorks()
    {
        yield [$this->values1, 1, 1];
        yield [$this->values3, 3, 3];
        yield [$this->values1x3, 1, 3];

        yield [$this->values1, 1, null];
        yield [$this->values3, null, 3];
        yield [$this->values1x3, 1, null];
        yield [$this->values1x3, null, 3];
    }

    /**
     * @dataProvider provideTestWorks
     * @covers \Params\ProcessRule\MatrixIsSize
     */
    public function testWorks($testValue, ?int $row, ?int $columns)
    {
        $rule = new MatrixIsSize($row, $columns);
        $processedValues = new ProcessedValues();

        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue, $processedValues, $dataLocator
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($testValue, $validationResult->getValue());
    }

    public function provideTestErrors()
    {
        // Both set - rows wrong
        yield [$this->values1, 2, 1, Messages::MATRIX_MUST_BE_OF_SIZE];
        yield [$this->values3, 2, 3, Messages::MATRIX_MUST_BE_OF_SIZE];
        yield [$this->values1x3, 2, 3, Messages::MATRIX_MUST_BE_OF_SIZE];

        // Both set - column wrong
        yield [$this->values1, 1, 2, Messages::MATRIX_MUST_BE_OF_SIZE];
        yield [$this->values3, 3, 2, Messages::MATRIX_MUST_BE_OF_SIZE];
        yield [$this->values1x3, 1, 2, Messages::MATRIX_MUST_BE_OF_SIZE];

        // One set - rows wrong
        yield [$this->values1, 2, null, Messages::MATRIX_MUST_BE_OF_ROW_SIZE];
        yield [$this->values1x3, 2, null, Messages::MATRIX_MUST_BE_OF_ROW_SIZE];

        // One set - columns wrong
        yield [$this->values3, null, 2, Messages::MATRIX_MUST_BE_OF_COLUMN_SIZE];
        yield [$this->values1x3, null, 2, Messages::MATRIX_MUST_BE_OF_COLUMN_SIZE];
        yield [$this->values1x3, null, 4, Messages::MATRIX_MUST_BE_OF_COLUMN_SIZE];
    }

    /**
     * @dataProvider provideTestErrors
     * @covers \Params\ProcessRule\MatrixIsSize
     */
    public function testErrors($testValue, ?int $row, ?int $columns, $expectedErrorMessage)
    {
        $rule = new MatrixIsSize($row, $columns);
        $processedValues = new ProcessedValues();
        $dataLocator = ArrayInputStorage::fromSingleValue('foo', $testValue);

        $validationResult = $rule->process(
            $testValue,
            $processedValues,
            $dataLocator
        );

        $this->assertValidationProblemRegexp(
            '/foo',
            $expectedErrorMessage,
            $validationResult->getValidationProblems()
        );

        if ($row === null) {
            $message = sprintf("has %d", count($testValue[0]));
        }
        else if ($columns === null) {
            $message = sprintf("has %d", count($testValue));
        }
        else {
            $message = sprintf("but is %d x %d", count($testValue), count($testValue[0]));
        }
        $validationProblem = $validationResult->getValidationProblems()[0];
        $this->assertStringContainsString(
            $message,
            $validationProblem->getProblemMessage()
        );
    }

    /**
     * @covers \Params\ProcessRule\MatrixIsSize
     */
    public function testDescription()
    {
        $description = new OpenApiV300ParamDescription('John');
        $rule = new MatrixIsSize(3, 3);
        $rule->updateParamDescription($description);
    }
}
