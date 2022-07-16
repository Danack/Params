<?php

declare(strict_types = 1);

namespace TypeSpecTest\ExtractRule;

use TypeSpec\ExtractRule\GetArrayOfInt;
use TypeSpec\DataStorage\TestArrayDataStorage;
use TypeSpec\Messages;
use TypeSpecTest\BaseTestCase;
use TypeSpec\ExtractRule\GetInt;
use TypeSpec\ProcessedValues;
use TypeSpec\ExtractRule\GetKernelMatrixOrDefault;

/**
 * @coversNothing
 */
class GetKernelMatrixOrDefaultTest extends BaseTestCase
{
    const UNIT_MATRIX = [[1]];

    public function provideTestWorks()
    {
        $expected = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];
        yield [json_encode($expected), $expected, self::UNIT_MATRIX];
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testWorks($input, $expected, $default)
    {
        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', $input);

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $expected);
    }


    /**
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testMissingGivesDefault()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];


        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();

        $dataStorage = TestArrayDataStorage::createMissing('foo');

        $validationResult = $rule->process(
            $validator,
            $dataStorage
        );

        $this->assertNoProblems($validationResult);
        $this->assertEquals($validationResult->getValue(), $default);
    }




    /**
     * * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testInvalidMatrixRows()
    {
        $default = [
            [1, 2, 3],
            'John'
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::MATRIX_INVALID_BAD_ROW
        );

        new GetKernelMatrixOrDefault($default);
    }

    /**
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testInvalidMatrixCell()
    {
        $default = [
            [1, 2, 3],
            [1, 2, 'John'],
        ];

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::MATRIX_INVALID_BAD_CELL
        );

        new GetKernelMatrixOrDefault($default);
    }



    /**
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     */
    public function testDescription()
    {
        $default = [[1.5]];

        $rule = new GetKernelMatrixOrDefault($default);
        $description = $this->applyRuleToDescription($rule);
        // TODO - inspect description
    }



    /**
     * @covers \TypeSpec\ExtractRule\GetKernelMatrixOrDefault
     * @dataProvider provideTestWorks
     */
    public function testBadInput_not_a_string()
    {
        $default = [
            [-1, -1, -1],
            [-1, 8, -1],
            [-1, -1, -1],
        ];

        $rule = new GetKernelMatrixOrDefault($default);
        $validator = new ProcessedValues();
        $dataStorage = TestArrayDataStorage::fromSingleValueAndSetCurrentPosition('foo', 123);

        $this->expectExceptionMessageMatchesTemplateString(
            Messages::BAD_TYPE_FOR_KERNEL_MATRIX_PROCESS_RULE
        );

        $rule->process(
            $validator,
            $dataStorage
        );
    }
}
